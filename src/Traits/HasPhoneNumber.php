<?php

namespace Tools\Traits;

use Tools\Services\TextService;

trait HasPhoneNumber
{
    public function setPhoneAttribute(string $value)
    {
        $this->attributes['phone'] = TextService::cleanPhoneForDatabase($value);
    }
}
