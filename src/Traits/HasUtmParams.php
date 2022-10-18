<?php

namespace Tools\Traits;

/**
 * @property array $utm_params
 */
trait HasUtmParams
{
    public function initializeHasUtmParams()
    {
        $this->casts['utm_params'] = 'array';
        $this->fillable[] = 'utm_params';
    }

    public function getUtmParam(string $name): string
    {
        if (isset($this->utm_params[$name])) {
            return $this->utm_params[$name];
        }

        return '';
    }

    public function getUtmString($separator = ', '): ?string
    {
        if (empty($this->utm_params)) {
            return null;
        }

        return implode($separator, array_filter($this->utm_params));
    }
}
