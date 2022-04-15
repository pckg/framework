<?php

namespace Pckg\Framework\Provider\Helper;

trait PostValidationResolver
{
    protected $validator;

    public function validator(callable $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    public function validate($resolved)
    {
        if ($this->validator) {
            ($this->validator)($resolved);
        }

        return $resolved;
    }
}
