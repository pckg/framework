<?php

namespace Pckg\Framework\View;

class TwigObjectizerNodeVisitor implements \Twig_NodeVisitorInterface
{
    protected $inAModule = false;
    protected $tags;
    protected $filters;
    protected $functions;

    /**
     * Called before child nodes are visited.
     *
     * @param Twig_NodeInterface $node The node to visit
     * @param Twig_Environment   $env The Twig environment instance
     *
     * @return Twig_NodeInterface The modified node
     */
    public function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Expression_Name && $node->getAttribute('name') == 'form') {
            $node->setAttribute('safe', ['all']);
        } elseif ($node instanceof \Twig_Node_Expression_GetAttr && $node->getAttribute('type') == 'method') {
            foreach ($node->getIterator() as $subnode) {
                $this->enterNode($subnode, $env);
            }

        }

        return $node;
    }

    /**
     * Called after child nodes are visited.
     *
     * @param Twig_NodeInterface $node The node to visit
     * @param Twig_Environment   $env The Twig environment instance
     *
     * @return Twig_NodeInterface The modified node
     */
    public function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -1;
    }
}
