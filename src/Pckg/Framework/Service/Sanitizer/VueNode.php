<?php

namespace Pckg\Framework\Service\Sanitizer;

use HtmlSanitizer\Node\AbstractNode;
use HtmlSanitizer\Node\HasChildrenTrait;

class VueNode extends AbstractNode
{
    use HasChildrenTrait;

    public function render(): string
    {
        return 'norender';
    }

    /*public function getTagName(): string
    {
        return 'del';
    }*/

}
