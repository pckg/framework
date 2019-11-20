<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Provider;
use Pckg\Translator\Service\Translator as TranslatorService;

class Translator extends Provider
{

    public function viewObjects()
    {
        return [
            '_translator' => TranslatorService::class,
        ];
    }

}