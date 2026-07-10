<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', 'salones-webhook'));

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        try {
            $entry = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value = $changes['value'] ?? null;
            $messages = $value['messages'] ?? [];

            foreach ($messages as $msg) {
                $from = $msg['from'] ?? null;
                $text = $msg['text']['body'] ?? '';
                $profileName = $value['contacts'][0]['profile']['name'] ?? 'Cliente';

                if ($from && $text) {
                    $processed = WhatsAppService::processIncoming([
                        'from' => $from,
                        'message' => $text,
                        'name' => $profileName,
                    ]);

                    if (config('services.whatsapp.token')) {
                        WhatsAppService::sendMessage($from, $processed);
                    }
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}