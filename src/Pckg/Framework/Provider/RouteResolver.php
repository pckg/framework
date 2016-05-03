<?php namespace Pckg\Framework\Provider;

interface RouteResolver
{

    public function resolve($class);

    public function parametrize($record);

}