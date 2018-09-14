<?php

namespace phpDOMExtend;

use DOMXPath;

class DOMFunctions
{
    function __construct()
    {
        ;
    }

    public static function doXPathQuery($node, $expression, $context_node = null)
    {
        $node_parent = $node->ownerDocument;

        if(is_null($node_parent))
        {
            $xpath = new DOMXPath($node);
        }
        else
        {
            $xpath = new DOMXPath($node->ownerDocument);
        }

        if(is_null($context_node))
        {
            $context_node = $this;
        }

        return $xpath->query($expression, $context_node);
    }
}