<?php

namespace Tools\Services;

use DateTimeInterface;

class TextService
{
    /**
     * Reformats text according to Telegram's formatting requirements
     * @link https://core.telegram.org/bots/api#formatting-options
     */
    public static function prepareForTelegram(string $text): string
    {
        // new lines
        $text = str_replace([
            '<br>',
            '<br/>',
            '<br />',
            '↵',
            "\n",
        ], "\r\n", $text);
        $text = str_replace([
            "\r\n\n",
        ], "\r\n", $text);

        $text = strip_tags($text, '<b><strong><i><em><u><ins><s><strike><del><a><code><pre>');

        // named entities to numeric ones
        $text = preg_replace_callback('/(&[a-zA-Z][a-zA-Z0-9]*;)/', function ($m) {
            $c = html_entity_decode($m[0], ENT_HTML5, 'UTF-8');

            $convmap = [0x80, 0xffff, 0, 0xffff];

            return mb_encode_numericentity($c, $convmap, 'UTF-8');
        }, $text);

        return $text;
    }

    public static function formatDate(DateTimeInterface $date): string
    {
        $months = [
            '',
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря',
        ];

        return $date->format('j') . ' ' . $months[$date->format('n')];
    }

    public static function formatDateTime(DateTimeInterface $dateTime, string $timeFormat = 'G:i'): string
    {
        return self::formatDate($dateTime) . ' в ' . $dateTime->format($timeFormat);
    }

    public static function cleanPhoneForDatabase($phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        $phone = (string)$phone;

        if (empty($phone)) {
            return '';
        }

        if ($phone[0] == '8') {
            $phone[0] = '7';
        }
        if ($phone[0] == '9') {
            $phone = '7' . $phone;
        }

        return $phone;
    }

    public static function formatPhoneForDisplay($phone): string
    {
        return preg_replace(
            '/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/',
            '+$1 $2 $3-$4-$5',
            $phone
        );
    }

    public static function getNiceEnding(float $number, array $endings): string
    {
        if (!isset($endings[2])) {
            $endings[2] = $endings[1];
        }

        $number = $number % 100;

        if ($number >= 11 && $number <= 19) {
            $ending = $endings[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case 1:
                    $ending = $endings[0];
                    break;
                case 2:
                case 3:
                case 4:
                    $ending = $endings[1];
                    break;
                default:
                    $ending = $endings[2];
            }
        }

        return $ending;
    }

    /**
     * Masks email local part, leaving only the first character
     *
     * "m@gmail.com" -> "m***@gmail.com"
     * "mail@gmail.com" -> "m***@gmail.com"
     */
    public static function maskEmail(string $email): string
    {
        return substr($email, 0, 1) . '***' . substr($email, strpos($email, '@'));
    }

    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }
}
