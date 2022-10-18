<?php

namespace Tools\Traits;

use Exception;
use Illuminate\Support\Facades\URL;
use Tools\Services\TextService;
use UrlLogin\Traits\AuthenticatesViaUrl;
use WeStacks\TeleBot\Laravel\TeleBot;
use WeStacks\TeleBot\Objects\Message;
use WeStacks\TeleBot\Objects\User;
use WeStacks\TeleBot\TeleBot as TeleBotBot;

trait AuthenticatesViaTelegram
{
    use AuthenticatesViaUrl;

    public static function getAuthBot(): TeleBotBot
    {
        return TeleBot::bot();
    }

    public function connectTelegram(User $user)
    {
        if (! empty($this->tg_id) || ! empty($this->tg_username)) {
            throw new Exception('Already linked');
        }

        $this->fillFromTgUser($user);

        $this->generateAndSendAuthUrl();
    }

    public static function generateTelegramAuthRequestUrl(array $params = []): string
    {
        return 'https://t.me/' . static::getAuthBot()->getConfig()['name'] . '?start=admin_auth_'
            . TextService::base64UrlEncode(http_build_query($params));
    }

    public function generateAndSendAuthUrl(array $parameters = []): Message
    {
        $tokenParameters = $this->createUrlLoginToken();

        $parameters['token_id'] = $tokenParameters['public_id'];
        $parameters['token'] = $tokenParameters['token'];

        $tokenLifeTime = config('url-login.auth_token_lifetime');

        // signed with no lifetime so we can seamlessly regenerate the url after expiration
        $url = url(
            Url::signedRoute(
                'admin.auth.telegram',
                $parameters,
                null,
                false
            )
        );

        return static::getAuthBot()->sendMessage([
            'chat_id' => $this->tg_id,
            'text' => 'Ссылка действует ' . $tokenLifeTime . ' минут'
                . TextService::getNiceEnding($tokenLifeTime, ['у', 'ы', '']),
            'parse_mode' => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Войти',
                            'url' => $url,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
