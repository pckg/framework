<?php namespace Pckg\Framework\Service\Sanitizer;

use HtmlSanitizer\Extension\Basic\Node\DelNode;
use HtmlSanitizer\Model\Cursor;
use HtmlSanitizer\Node\NodeInterface;
use HtmlSanitizer\Visitor\AbstractNodeVisitor;
use HtmlSanitizer\Visitor\HasChildrenNodeVisitorTrait;

class VueVisitor extends AbstractNodeVisitor
{

    use HasChildrenNodeVisitorTrait;

    public function supports(\DOMNode $domNode, Cursor $cursor): bool
    {
        return in_array($domNode->nodeName, ['a', 'img', 'p', 'span']);
    }

    protected function createNode(\DOMNode $domNode, Cursor $cursor): NodeInterface
    {
        return new VueNode($cursor->node);
    }

}