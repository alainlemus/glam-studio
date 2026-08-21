<?php
    $client = $appointment->client;
    $branch = $appointment->branch;
    $stylistName = $appointment->stylist?->user?->name;
    $serviceNames = $appointment->services->map(fn ($s) => $s->service?->name)->filter()->implode(', ');
    $siteName = $settings->site_name ?: config('app.name');
    $logoUrl = $settings->logo_url ?: url('/images/logo.png');
    $dateLabel = $appointment->date->translatedFormat('l d \d\e F \d\e Y');
    $timeLabel = \Illuminate\Support\Carbon::parse($appointment->start_time)->format('g:i a');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $siteName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#0A0A0A; font-family: Georgia, 'Times New Roman', serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0A0A0A; padding: 32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 560px; background-color:#141414; border:1px solid #2A2A2A; border-radius: 16px; overflow:hidden;">

    <!-- Header -->
    <tr>
        <td align="center" style="padding: 36px 32px 20px; background: linear-gradient(180deg, #1F1F1F 0%, #141414 100%); border-bottom: 1px solid #2A2A2A;">
            <img src="{{ $logoUrl }}" width="72" height="72" alt="{{ $siteName }}" style="display:block; margin: 0 auto 12px;">
            <div style="font-family: Georgia, serif; font-size: 22px; font-style: italic; color: #E8E8E8; letter-spacing: 0.5px;">{{ $siteName }}</div>
        </td>
    </tr>

    <!-- Eyebrow + Title -->
    <tr>
        <td style="padding: 32px 32px 8px;" align="center">
            <div style="font-family: Arial, sans-serif; font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #D1D5DB; margin-bottom: 10px;">
                @if($forStaff)
                    Nueva reservación
                @else
                    Reservación confirmada
                @endif
            </div>
            <div style="font-family: Georgia, serif; font-size: 26px; color: #FAF7F2; line-height: 1.3;">
                @if($forStaff)
                    {{ $client?->name }} agendó una cita
                @else
                    Tu belleza, <span style="font-style: italic; color: #E8E8E8;">nuestra pasión</span>
                @endif
            </div>
            @unless($forStaff)
            <div style="font-family: Arial, sans-serif; font-size: 14px; color: #9CA3AF; margin-top: 10px; line-height: 1.6;">
                Hola {{ $client?->name }}, tu cita quedó registrada. Te esperamos.
            </div>
            @endunless
        </td>
    </tr>

    <!-- Código -->
    <tr>
        <td align="center" style="padding: 8px 32px 24px;">
            <table role="presentation" cellpadding="0" cellspacing="0" style="background-color: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.3); border-radius: 999px;">
                <tr>
                    <td style="padding: 8px 20px; font-family: 'Courier New', monospace; font-size: 13px; letter-spacing: 1px; color: #E5C158;">
                        {{ $appointment->code }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Detalles de la cita -->
    <tr>
        <td style="padding: 0 32px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#1F1F1F; border:1px solid #2A2A2A; border-radius: 14px;">
                <tr>
                    <td style="padding: 22px 24px; font-family: Arial, sans-serif; font-size: 14px; color: #D4D4D8;">

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding: 6px 0; color:#9CA3AF; width: 40%;">Servicio</td>
                                <td style="padding: 6px 0; color:#FAF7F2; font-weight: bold; text-align:right;">{{ $serviceNames }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color:#9CA3AF;">Fecha</td>
                                <td style="padding: 6px 0; color:#FAF7F2; text-align:right; text-transform: capitalize;">{{ $dateLabel }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color:#9CA3AF;">Hora</td>
                                <td style="padding: 6px 0; color:#FAF7F2; text-align:right;">{{ $timeLabel }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color:#9CA3AF;">Sucursal</td>
                                <td style="padding: 6px 0; color:#FAF7F2; text-align:right;">{{ $branch?->name }}</td>
                            </tr>
                            @if($stylistName)
                            <tr>
                                <td style="padding: 6px 0; color:#9CA3AF;">Estilista</td>
                                <td style="padding: 6px 0; color:#FAF7F2; text-align:right;">{{ $stylistName }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2" style="border-top: 1px solid #2A2A2A; padding-top: 14px; margin-top: 8px;"></td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; color:#D4D4D8; font-weight: bold;">Total</td>
                                <td style="padding: 6px 0; color:#D4AF37; font-family: Georgia, serif; font-size: 20px; font-weight:bold; text-align:right;">
                                    {{ 'MX$' . number_format((float) $appointment->total, 2) }}
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($forStaff)
    <!-- Datos de contacto del cliente (solo staff) -->
    <tr>
        <td style="padding: 0 32px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: rgba(209,213,219,0.06); border:1px solid #2A2A2A; border-radius: 14px;">
                <tr>
                    <td style="padding: 20px 24px; font-family: Arial, sans-serif; font-size: 14px; color: #D4D4D8;">
                        <div style="font-family: Arial, sans-serif; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #E8E8E8; margin-bottom: 10px;">Datos de la clienta</div>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding: 4px 0; color:#9CA3AF; width: 40%;">Nombre</td>
                                <td style="padding: 4px 0; color:#FAF7F2; text-align:right;">{{ $client?->name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color:#9CA3AF;">Teléfono</td>
                                <td style="padding: 4px 0; color:#FAF7F2; text-align:right;">{{ $client?->phone }}</td>
                            </tr>
                            @if($client?->email)
                            <tr>
                                <td style="padding: 4px 0; color:#9CA3AF;">Correo</td>
                                <td style="padding: 4px 0; color:#FAF7F2; text-align:right;">{{ $client?->email }}</td>
                            </tr>
                            @endif
                            @if($appointment->notes)
                            <tr>
                                <td style="padding: 4px 0; color:#9CA3AF; vertical-align: top;">Notas</td>
                                <td style="padding: 4px 0; color:#FAF7F2; text-align:right;">{{ $appointment->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <!-- CTA -->
    <tr>
        <td align="center" style="padding: 0 32px 32px;">
            @if($forStaff)
                <a href="{{ url('/admin/appointments/' . $appointment->id) }}" style="display:inline-block; background-color:#E8E8E8; color:#0A0A0A; font-family: Arial, sans-serif; font-size: 14px; font-weight:bold; letter-spacing: 0.5px; text-decoration:none; padding: 14px 32px; border-radius: 999px;">
                    Ver cita en el panel
                </a>
            @else
                <div style="font-family: Arial, sans-serif; font-size: 13px; color:#9CA3AF; line-height: 1.7;">
                    Si necesitas cambiar o cancelar tu cita, contáctanos directamente en la sucursal.
                </div>
                @if($branch?->whatsapp)
                <div style="margin-top: 16px;">
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $branch->whatsapp) }}" style="display:inline-block; background-color:#E8E8E8; color:#0A0A0A; font-family: Arial, sans-serif; font-size: 14px; font-weight:bold; letter-spacing: 0.5px; text-decoration:none; padding: 14px 32px; border-radius: 999px;">
                        WhatsApp de la sucursal
                    </a>
                </div>
                @endif
            @endif
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="padding: 24px 32px; border-top: 1px solid #2A2A2A; background-color:#0F0F0F;" align="center">
            <div style="font-family: Arial, sans-serif; font-size: 12px; color:#6B7280; line-height: 1.8;">
                {{ $branch?->name }}<br>
                {{ $branch?->address }}<br>
                @if($branch?->phone)
                    Tel: {{ $branch->phone }}
                @endif
            </div>
            <div style="font-family: Arial, sans-serif; font-size: 11px; color:#4B5563; margin-top: 16px;">
                © {{ date('Y') }} {{ $siteName }} · Todos los derechos reservados
            </div>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>
