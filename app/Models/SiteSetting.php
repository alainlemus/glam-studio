<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'tagline', 'footer_description', 'notification_email', 'logo_path',
        'instagram_url', 'facebook_url', 'tiktok_url', 'privacy_policy', 'privacy_policy_updated_at',
    ];

    protected $casts = [
        'privacy_policy_updated_at' => 'datetime',
    ];

    /**
     * There is always exactly one settings row. Fetch it, creating it
     * with defaults on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'footer_description' => 'Belleza, estilo y bienestar en cada visita. Tu salón de confianza.',
            'privacy_policy' => self::defaultPrivacyPolicy(),
            'privacy_policy_updated_at' => now(),
        ]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public static function defaultPrivacyPolicy(): string
    {
        return <<<'HTML'
<p>En Glam Studio nos tomas en serio la protección de tus datos personales. Este aviso explica de forma clara qué información recabamos cuando usas este sitio, para qué la usamos y cómo puedes ejercer tus derechos sobre ella.</p>

<h2>1. Responsable del tratamiento de tus datos</h2>
<p>Glam Studio, a través de sus sucursales indicadas en la sección "Sucursales" de este sitio, es responsable del uso y protección de tus datos personales conforme a la legislación aplicable en materia de protección de datos.</p>

<h2>2. Datos personales que recabamos</h2>
<p>Cuando agendas una cita desde este sitio recabamos directamente de ti:</p>
<ul>
<li>Nombre completo</li>
<li>Número de teléfono</li>
<li>Correo electrónico</li>
<li>Notas u observaciones adicionales que decidas compartir sobre tu cita</li>
<li>Sucursal, servicio, estilista (si lo eliges) y fecha/hora seleccionados</li>
</ul>
<p>No recabamos datos financieros ni de tarjetas de pago a través de este sitio: los pagos se realizan directamente en la sucursal.</p>
<p>Si nos escribes por WhatsApp, tu número de teléfono y el contenido de tus mensajes son procesados a través de la plataforma de WhatsApp Business de Meta Platforms, Inc. para poder atenderte y darte seguimiento.</p>

<h2>3. Finalidades del tratamiento</h2>
<p><strong>Finalidades primarias</strong> (necesarias para darte el servicio): agendar, confirmar y dar seguimiento a tu cita; enviarte la confirmación de tu reservación por correo electrónico; contactarte por teléfono o WhatsApp para recordatorios o cambios de horario; y atender tus dudas o solicitudes.</p>
<p><strong>Finalidades secundarias</strong> (opcionales, puedes oponerte en cualquier momento): informarte sobre promociones, nuevos servicios o novedades del salón.</p>

<h2>4. Transferencia de tus datos</h2>
<p>Tus datos se comparten únicamente con la sucursal donde agendaste tu cita, para efectos de operación interna. Cuando nos escribes por WhatsApp, tu número y mensajes son procesados por Meta Platforms, Inc. como proveedor de dicha plataforma de mensajería.</p>
<p>No vendemos, rentamos ni compartimos tus datos personales con terceros para fines distintos a los aquí descritos.</p>

<h2>5. Cookies y almacenamiento local</h2>
<p>Este sitio utiliza almacenamiento local del navegador únicamente para recordar tu preferencia de apariencia (modo claro u oscuro) y, si lo aceptas, para recordar que ya confirmaste este aviso de privacidad. No utilizamos cookies de rastreo publicitario ni herramientas de análisis de terceros.</p>

<h2>6. Testimonios</h2>
<p>Si compartes con nosotros una reseña o testimonio, tu nombre y, en su caso, tu fotografía podrán publicarse en este sitio únicamente con tu autorización previa.</p>

<h2>7. Derechos de acceso, rectificación, cancelación y oposición (ARCO)</h2>
<p>Puedes acceder, rectificar o cancelar tus datos personales, así como oponerte al uso que les damos, escribiéndonos al correo de contacto indicado en la sección de sucursales, o acudiendo directamente a la sucursal donde agendaste tu cita.</p>

<h2>8. Cambios a este aviso</h2>
<p>Podemos actualizar este aviso de privacidad en cualquier momento. La fecha de la última actualización se indica al final de esta página.</p>
HTML;
    }
}
