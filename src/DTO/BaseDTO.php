<?php

namespace App\DTO;

abstract class BaseDTO
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    public function toArray(): array
    {
        return object_to_array($this);
    }
}
