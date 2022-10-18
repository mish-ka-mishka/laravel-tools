<?php

namespace Tools\Traits;

use Illuminate\Support\Collection;

/** @property Collection socials */
trait HasSocials
{
    public function initializeHasSocials()
    {
        $this->casts['socials'] = 'collection';
        $this->fillable[] = 'socials';
    }

    protected static $validSocials = [
        [
            'domains' => [
                'facebook.com',
                'fb.com',
            ],
            'title' => 'ФБ',
        ],
        [
            'domains' => [
                'instagram.com',
                'instagr.am',
            ],
            'title' => 'Инстаграм',
        ],
        [
            'domains' => [
                'twitter.com',
            ],
            'title' => 'Твиттер',
        ],
        [
            'domains' => [
                'vk.com',
                'm.vk.com',
            ],
            'title' => 'ВК',
        ],
        [
            'domains' => [
                'youtube.com',
            ],
            'title' => 'ЮТ',
        ],
        [
            'domains' => [
                't.me',
            ],
            'title' => 'Телеграм',
        ],
        [
            'domains' => [
                'github.com',
            ],
            'title' => 'Гитхаб',
        ],
    ];

    public function getSocialsFormatted(): ?Collection
    {
        if (empty($this->socials)) {
            return null;
        }

        $formatted = collect();

        foreach ($this->socials as $social) {
            $domain = mb_strtolower(parse_url($social, PHP_URL_HOST), 'UTF-8');
            $title = $domain;

            foreach (self::$validSocials as $item) {
                if (in_array($domain, $item['domains'])) {
                    $title = $item['title'];
                    break;
                }
            }

            $formatted->push([
                'url' => $social,
                'title' => $title,
            ]);
        }

        return $formatted;
    }

    public function getSocialsHtml($separator = '<br>'): ?string
    {
        if ($this->socials->isEmpty()) {
            return null;
        }

        return $this->socials->map(function (string $social) {
            return '<a href="' . $social . '" target="_blank" rel="noopener noreferrer">' . urldecode($social) . '</a>';
        })->implode($separator);
    }
}
