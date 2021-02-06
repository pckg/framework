<?php

namespace Pckg\Framework\Provider;

interface RouteResolver
{

    public function resolve($value);
    public function parametrize($record);
}
