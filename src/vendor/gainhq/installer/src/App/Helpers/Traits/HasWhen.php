<?php

namespace Gainhq\Installer\App\Helpers\Traits;

trait HasWhen
{
    /**
     * @param $value
     * @param callable $callback
     * @param callable|null $fallback
     * @return $this|HasWhen
     */
    public function when($value, callable $callback, $fallback = null)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }elseif ($fallback) {
            return $fallback($this, $value) ?: $this;
        }

        return $this;
    }
}
