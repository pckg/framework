<?php

namespace Pckg\Framework\Provider;

interface AutoloaderManager
{

    public function autoload();

    public function afterAutoload();

}