<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\PostResolver\Globals;
use Pckg\Framework\Request\Data\PostResolver\PostSource;

class Post extends Lazy
{
    protected $source = Globals::class;

    public function setSource(PostSource $postSource)
    {
        $this->source = $postSource;

        return $this;
    }

    public function getSource()
    {
        if (is_string($this->source)) {
            $this->source = resolve($this->source);
        }

        return $this->source;
    }

    public function setFromGlobals()
    {
        $this->setData($this->getSource()->readFromSource());

        return $this;
    }
}
