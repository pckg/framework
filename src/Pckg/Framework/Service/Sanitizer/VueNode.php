<?php

namespace Pckg\Framework\Service\Sanitizer;

use HtmlSanitizer\Node\AbstractNode;
use HtmlSanitizer\Node\HasChildrenTrait;

class VueNode extends AbstractNode
{
    use HasChildrenTrait;

    /*public function getTagName(): string
    {
        return 'del';
    }*/
}
