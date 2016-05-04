<?php namespace Pckg\Framework\Provider;

interface RouteResolver
{

    public function resolve();

    public function parametrize($record);

}