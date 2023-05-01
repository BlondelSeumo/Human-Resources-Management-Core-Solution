<?php

namespace Gainhq\Installer\App\Helpers\Traits;

use Illuminate\Support\Arr;

trait HasAttrs
{
    protected array $attributes = [];

    public function setAttrs(array $attrs): self
    {
        $this->attributes = $attrs;

        return $this;
    }

    public function setAttribute($key, $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function setAttr($key, $value): self
    {
        $this->setAttribute($key, $value);

        return $this;
    }

    public function mergeAttributes(array $attrs): self
    {
        $this->attributes = array_merge($this->attributes, $attrs);

        return $this;
    }

    public function mergeAttrs(array $attrs): self
    {
        $this->mergeAttributes($attrs);

        return $this;
    }

    public function getAttributes($columns = null): array
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        if (count($columns)) {
            return Arr::only($this->attributes, $columns);
        }

        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttrs($columns = null): array
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        return $this->getAttributes($columns);
    }

    public function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : $default;
    }

    public function getAttr($key, $default = null)
    {
        return $this->getAttribute($key, $default);
    }

    public function getFillAble($parameters = []): array
    {
        return count($this->attributes) ? $this->attributes : $parameters;
    }

    public function hasAttribute($key): bool
    {
        return !!array_key_exists($key, $this->attributes);
    }

    public function hasAttributes($keys = null): bool
    {
        if (!$this->attributes) {
            return false;
        }

        $keys = is_array($keys) ? $keys : array_filter(func_get_args());

        if (Arr::isAssoc($keys)) {
            $keys = array_keys($keys);
        }

        foreach ($keys as $k) {
            if (!array_key_exists($k, $this->attributes)) return false;
        }

        return true;
    }

    public function hasAttr($key): bool
    {
        return $this->hasAttribute($key);
    }

    public function hasAttrs($keys = null): bool
    {
        return $this->hasAttributes($keys);
    }

    public function filledAttr($key): bool
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }

        return filled($this->attributes[$key]);
    }
}