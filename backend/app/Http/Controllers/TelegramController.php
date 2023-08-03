<?php

namespace App\Http\Controllers;

use App\Models\Telegram;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function callback(Request $request)
    {
        Log::info("TG CALLBACK", $request->all());
        $message = $request->get("message");
        $tgUser = $message['from'];
        if (!isset($message['text'])) {
            return;
        }
        $text = $message['text'];
        if ($text[0] == "/") {
            $command = explode(" ", $text);
            if ($command[0] == "/start") {
                if (isset($command[1])) {
                    list($prefix, $uid) = explode("-", $command[1]);

                    User::query()->where("id", $uid)->update([
                        'tg_channel_id' => $tgUser['id'],
                        'tg_username' => $tgUser['username'] ?? $tgUser['id'],
                    ]);

                    Telegram::request("sendMessage", [
                        'chat_id' => $tgUser['id'],
                        "text" => "Вы успешно подписались на уведомления"
                    ]);
                } else {
                    Telegram::request("sendMessage", [
                        'chat_id' => $tgUser['id'],
                        "text" => "Не удалось связать с аккаунтом."
                    ]);
                }
            }
        }
    }
}
