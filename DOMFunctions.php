<?php

namespace phpDOMExtend;

use DOMXPath;

class DOMFunctions
{
    function __construct()
    {
        ;
    }

    public static function doXPathQuery($node, $expression, $context_node = null, $array = false)
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
            $context_node = $node;
        }

        return $xpath->query($expression, $context_node);
    }

    public static function insertAfter($node, $newnode, $refnode = null)
    {
        if(is_null($refnode))
        {
            return $node->appendChild($newnode);
        }

        $parent = $refnode->parentNode;
        $next = $refnode->nextSibling;

        if(!is_null($next))
        {
            return $parent->insertBefore($newnode, $next);
        }
        else
        {
            return $parent->appendChild($newnode);
        }

        return $newnode;
    }

    public static function getInnerNodes($node, $as_list = false)
    {
        $nodes = $node->childNodes;

        if ($as_list)
        {
            return $nodes;
        }

        $inner_dom = new ExtendedDOMDocument();

        foreach ($nodes as $node)
        {
            $inner_dom->appendChild($inner_dom->importNode($node, true));
        }

        return $inner_dom;
    }

    public static function attributeListToArray($node_list, $attribute_name)
    {
        $array = array();

        foreach ($node_list as $node)
        {
            $array[$node->getAttribute($attribute_name)] = $node;
        }

        return $array;
    }
}