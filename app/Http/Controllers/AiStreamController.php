<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\App;
use App\Models\SystemSetting;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiStreamController extends Controller
{
    public function stream(Request $request, AiConversation $conversation): StreamedResponse
    {
        // Validate ownership
        if ($conversation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if streaming is enabled
        if (! SystemSetting::get('ai_streaming_enabled', false)) {
            abort(400, 'Streaming is not enabled');
        }

        $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $message = $request->input('message');
        $user = auth()->user();
        $chatService = new AiChatService($user);

        // Set app context if conversation has an app
        if ($conversation->app_id) {
            $app = App::find($conversation->app_id);
            if ($app) {
                $chatService->setAppContext($app);
            }
        }

        // Check if user can chat
        if (! $chatService->canChat()) {
            return new StreamedResponse(function () {
                echo 'data: '.json_encode(['error' => 'Token limit reached'])."\n\n";
                flush();
            }, 200, $this->getStreamHeaders());
        }

        return new StreamedResponse(function () use ($chatService, $conversation, $message) {
            // Aggressively disable ALL output buffering
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            @ini_set('implicit_flush', true);

            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            try {
                $chatService->streamChat($conversation, $message, function ($chunk) {
                    echo 'data: '.json_encode(['content' => $chunk])."\n\n";
                    flush();
                });

                // Send completion event
                echo 'data: '.json_encode(['done' => true])."\n\n";
                flush();
            } catch (\Exception $e) {
                echo 'data: '.json_encode(['error' => $e->getMessage()])."\n\n";
                flush();
            }
        }, 200, $this->getStreamHeaders());
    }

    private function getStreamHeaders(): array
    {
        return [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
        ];
    }
}
