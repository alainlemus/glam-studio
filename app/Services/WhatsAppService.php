<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function linkForPhone(?string $phone, string $message = ''): ?string
    {
        if (!$phone) return null;
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $url = "https://wa.me/{$cleanPhone}";
        if ($message) {
            $url .= '?text=' . urlencode($message);
        }
        return $url;
    }

    public static function appointmentConfirmationLink(Appointment $appointment): string
    {
        $branch = $appointment->branch;
        if (!$branch || !$branch->whatsapp) {
            return self::linkForPhone($appointment->client?->phone, 'Hola');
        }

        $serviceName = $appointment->services->first()?->service?->name ?? 'servicio';
        $stylistName = $appointment->stylist?->user?->name ?? 'cualquier estilista disponible';
        $date = $appointment->date->format('d/m/Y');
        $time = substr($appointment->start_time, 0, 5);

        $message = "¡Hola! 👋 Soy {$appointment->client->name}, quiero confirmar mi cita:\n\n";
        $message .= "📅 {$date} a las {$time}\n";
        $message .= "💇 {$serviceName}\n";
        $message .= "👤 Con: {$stylistName}\n";
        $message .= "📍 Sucursal: {$branch->name}\n\n";
        $message .= "Código de cita: {$appointment->code}";

        return self::linkForPhone($branch->whatsapp, $message);
    }

    public static function reminderLink(Appointment $appointment): string
    {
        $client = $appointment->client;
        if (!$client || !$client->phone) return '';

        $branch = $appointment->branch;
        $serviceName = $appointment->services->first()?->service?->name ?? 'tu servicio';
        $date = $appointment->date->format('d/m/Y');
        $time = substr($appointment->start_time, 0, 5);

        $message = "¡Hola {$client->name}! 💇‍♀️✨\n\n";
        $message .= "Te recordamos tu cita mañana:\n";
        $message .= "📅 {$date} a las {$time}\n";
        $message .= "💇 {$serviceName}\n";
        $message .= "📍 {$branch?->name}\n\n";
        $message .= "¡Te esperamos! Confirma respondiendo este mensaje. ✅";

        return self::linkForPhone($client->phone, $message);
    }

    public static function sendMessage(string $phone, string $message): bool
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) {
            Log::info('WhatsApp no configurado, mensaje no enviado', [
                'phone' => $phone,
                'message' => $message,
            ]);
            ChatMessage::create([
                'phone' => $phone,
                'direction' => 'outgoing',
                'message' => $message,
                'is_bot' => true,
            ]);
            return true;
        }

        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v17.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $cleanPhone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            ChatMessage::create([
                'phone' => $phone,
                'direction' => 'outgoing',
                'message' => $message,
                'is_bot' => true,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function processIncoming(array $payload): string
    {
        $phone = $payload['from'] ?? null;
        $message = $payload['message'] ?? '';
        $name = $payload['name'] ?? 'Cliente';

        if (!$phone) return '';

        ChatMessage::create([
            'phone' => $phone,
            'direction' => 'incoming',
            'message' => $message,
            'is_bot' => false,
        ]);

        $intent = self::detectIntent($message);
        $response = self::generateResponse($intent, $phone, $name);

        ChatMessage::create([
            'phone' => $phone,
            'direction' => 'outgoing',
            'message' => $response,
            'intent' => $intent,
            'is_bot' => true,
        ]);

        return $response;
    }

    private static function detectIntent(string $message): string
    {
        $msg = mb_strtolower($message);

        if (preg_match('/(hola|buen[oa]s|hi|hello)/i', $msg)) return 'greeting';
        if (preg_match('/(agendar|cita|reservar|appointment)/i', $msg)) return 'booking';
        if (preg_match('/(servicio|precio|corte|tinte|manicure)/i', $msg)) return 'services';
        if (preg_match('/(horario|abren|cierran)/i', $msg)) return 'schedule';
        if (preg_match('/(ubicacion|donde|direccion|encuentran)/i', $msg)) return 'location';
        if (preg_match('/(gracias|thanks)/i', $msg)) return 'thanks';

        return 'unknown';
    }

    private static function generateResponse(string $intent, string $phone, string $name): string
    {
        $branch = \App\Models\Branch::active()->inRandomOrder()->first();
        $link = self::linkForPhone($branch?->whatsapp);

        return match ($intent) {
            'greeting' => "¡Hola {$name}! 👋💇‍♀️ Bienvenido(a) a *Salones Belleza*.\n\n¿En qué puedo ayudarte?\n\n1️⃣ Agendar una cita\n2️⃣ Ver servicios y precios\n3️⃣ Ubicación y horarios\n4️⃣ Hablar con un asesor",
            'booking' => "¡Perfecto! Para agendar tu cita, puedes hacerlo desde nuestro sitio web:\n\n🌐 " . url('/agendar') . "\n\nO si prefieres, responde con:\n- Tu nombre\n- Servicio que deseas\n- Fecha y hora preferida\n- Sucursal\n\nY te ayudamos a confirmar. 😊",
            'services' => "Tenemos muchos servicios para ti:\n\n💇‍♀️ Cortes y peinados\n🎨 Coloración\n💅 Uñas\n💄 Maquillaje\n👁️ Cejas y pestañas\n🧖 Spa y faciales\n\nConsulta todos con precios en:\n🌐 " . url('/servicios'),
            'schedule' => "🕐 Nuestro horario general es:\n\nLunes a Sábado: 9:00 - 20:00\nDomingos: Cerrado\n\nPuedes ver el horario específico de cada sucursal en:\n🌐 " . url('/sucursales'),
            'location' => "📍 Estamos en varias ciudades:\n\n• Ciudad de México (Polanco, Roma)\n• Guadalajara\n• Monterrey\n• Querétaro\n\nVer todas las ubicaciones:\n🌐 " . url('/sucursales'),
            'thanks' => "¡Gracias a ti! 💖 Si necesitas algo más, aquí estamos. ¡Te esperamos en *Salones Belleza*! ✨",
            default => "Gracias por tu mensaje. 😊\n\nPara brindarte mejor atención, escríbenos:\n\n📅 *Agendar cita:* " . url('/agendar') . "\n💇 *Ver servicios:* " . url('/servicios') . "\n📞 *WhatsApp directo:* {$link}",
        };
    }
}