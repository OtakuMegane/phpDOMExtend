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

    public static function copyNode($node, $target_node, $insert)
    {
        $parent = $target_node->parentNode;

        if ($insert === 'before')
        {
            return $parent->insertBefore($node->cloneNode(true), $target_node);
        }
        else if($insert === 'after')
        {
            return self::insertAfter($node->cloneNode(true), $target_node);
        }
        else if($insert === 'append')
        {
            return $target_node->appendChild($node->cloneNode(true));
        }

        return $node;
    }

    public static function getAssociativeNodeArray($node, $name, $context_node = null)
    {
        $array = array();
        $node_list = self::doXPathQuery($node, ".//*[@" . $name . "]", $context_node);

        foreach ($node_list as $node)
        {
            $array[$node->getAttribute($name)] = $node;
        }

        return $array;
    }
}