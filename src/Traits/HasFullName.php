<?php

namespace Tools\Traits;

trait HasFullName
{
    public function getFullName(): string
    {
        $fullname = [
            $this->surname,
            $this->name,
            $this->middlename,
        ];

        $fullname = array_filter($fullname);

        return implode(' ', $fullname);
    }

    public function getShortName(bool $invert = false): string
    {
        $shortname = [
            $this->name,
            $this->surname,
        ];

        if ($invert) {
            $shortname = array_reverse($shortname);
        }

        $shortname = array_filter($shortname);

        return implode(' ', $shortname);
    }
}
