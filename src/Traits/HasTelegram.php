<?php

namespace Tools\Traits;

use WeStacks\TeleBot\Exception\TeleBotRequestException;
use WeStacks\TeleBot\Objects\User;

/**
 * @property ?int tg_id
 * @property ?string tg_username
 * @property ?string tg_phone
 * @property bool has_blocked_bot
 * @property ?string surname
 * @property ?string name
 */
trait HasTelegram
{
    public function hasConnectedTelegram(): bool
    {
        return ! empty($this->tg_id) && ! $this->has_blocked_bot;
    }

    public function getTelegramProfileUrl(bool $asHtml = false): ?string
    {
        if (empty($this->tg_username)) {
            return null;
        }

        $url = 'https://t.me/' . $this->tg_username;

        if (! $asHtml) {
            return $url;
        }

        return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">@' . $this->tg_username . '</a>';
    }

    public static function createFromTgUser(User $user): static
    {
        $model = new static();

        $model->fillFromTgUser($user);

        return $model;
    }

    public function fillFromTgUser(User $user): bool
    {
        $this->tg_id = $user->id;
        $this->tg_username = $user->username ?? null;
        $this->surname = $user->last_name ?? null;
        $this->name = $user->first_name;

        return $this->save();
    }

    public function checkIfTgUsernameChanged(?string $username): bool
    {
        if ($this->tg_username !== $username) {
            // $oldUsername = $this->tg_username;
            $this->tg_username = $username;
            $this->save();

            return true;
        }

        return false;
    }

    public function setBlockedTelegramBot()
    {
        if (! $this->has_blocked_bot) {
            $this->has_blocked_bot = true;
            $this->save();
        }
    }

    public function setUnblockedTelegramBot()
    {
        if ($this->has_blocked_bot) {
            $this->has_blocked_bot = false;
            $this->save();
        }
    }

    /**
     * @throws TeleBotRequestException
     */
    public function interceptTelegramExceptions(callable $callable): mixed
    {
        try {
            return $callable($this);
        } catch (TeleBotRequestException $e) {
            switch ($e->getMessage()) {
                case 'Bad Request: user not found':
                    // User has deleted telegram account

                    $this->tg_id = null;
                    $this->tg_phone = null;
                    $this->tg_username = null;
                    $this->save();

                    break;

                case 'Forbidden: bot was blocked by the user':
                    $this->setBlockedTelegramBot();

                    break;
            }

            throw $e;
        }
    }
}
