<?php

namespace phpDOMExtend;

use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMAttr;

class ExtendedDOMElement extends DOMElement
{
    function __construct($register = false)
    {
        parent::__construct();
    }

    /**
     * Execute an XPath query on the this node and return the result.
     *
     * @param string $expression The XPath query
     * @param DOMNode [optional] $context_node Optional context node to limit the query scope
     * @return DOMNodeList Returns result of the query as a DOMNodeList object
     */
    public function doXPathQuery($expression, $context_node = null)
    {
        return DOMHelperFunctions::doXPathQuery($this, $expression, $context_node);
    }

    /**
     * Extended setAttribute that adds escaping to the value.
     *
     * @param string $name Attribute name
     * @param string $value Attribute value
     * @param string $escape_type Type of escaping to use
     * @return DOMAttr The old node if replaced, otherwise null
     */
    public function extSetAttribute($name, $value, $escape_type = 'attribute')
    {
        DOMEscaper::doEscaping($value, $escape_type);
        $attribute = $this->ownerDocument->createAttribute($name);
        $attribute->value = $value;
        return $this->setAttributeNode($attribute);
    }

    /**
     * Extended setAttributeNS that adds escaping to the value.
     *
     * @param string $namespaceURI The URI of the namespace
     * @param string $qualifiedName The qualified name of the element
     * @param string $value Attribute value
     * @param string $escape_type Type of escaping to use
     * @return DOMAttr The old node if replaced, otherwise null
     */
    public function extSetAttributeNS($namespaceURI, $qualifiedName, $value, $escape_type = 'attribute')
    {
        DOMEscaper::doEscaping($value, $escape_type);
        $attribute = $this->ownerDocument->createAttributeNS($namespaceURI, $qualifiedName);
        $attribute->value = $value;
        return $this->setAttributeNodeNS($attribute);
    }

    /**
     * Modify an existing attribute or add if the attribute does not exist yet.
     *
     * @param string $name Attribute name
     * @param string $value Attribute value
     * @param string $relative How to modify the existing value
     * @param string $escape_type Type of escaping to use
     * @return string The original attribute value
     */
    public function modifyAttribute($name, $value, $relative = 'replace', $escape_type = 'attribute')
    {
        $existing_content = '';

        if ($this->hasAttribute($name))
        {
            $existing_content = $this->getAttribute($name);

            if ($relative === 'after')
            {
                $value = $existing_content . $value;
            }
            else if ($relative === 'before')
            {
                $value = $value . $existing_content;
            }
        }

        $this->extSetAttribute($name, $value, $escape_type);
        return $existing_content;
    }

    /**
     * Modify an existing namespaced attribute or add if the attribute does not exist yet.
     *
     * @param string $namespaceURI The URI of the namespace
     * @param string $qualifiedName The qualified name of the element
     * @param string $value Attribute value
     * @param string $relative How to modify the existing value
     * @param string $escape_type Type of escaping to use
     * @return string The original attribute value
     */
    public function modifyAttributeNS($namespaceURI, $qualifiedName, $value, $relative = 'replace', $escape_type = 'attribute')
    {
        $existing_content = '';

        if ($this->hasAttributeNS($namespaceURI, $localName))
        {
            $existing_content = $this->getAttributeNodeNS($namespaceURI, $localName);

            if ($relative === 'after')
            {
                $value = $existing_content . $value;
            }
            else if ($relative === 'before')
            {
                $value = $value . $existing_contents;
            }
        }

        $this->extSetAttributeNS($namespaceURI, $qualifiedName, $value, $escape_type);
        return $existing_content;
    }

    /**
     * Gets the current node value.
     *
     * @return string The current node value
     */
    public function getContent()
    {
        return $this->nodeValue;
    }

    /**
     * Sets the current node value.
     *
     * @param string $value Node value
     * @param string $relative How to modify the existing value
     * @param string $escape_type Type of escaping to use
     * @return string The old node value
     */
    public function setContent($value, $relative = 'replace', $escape_type = 'html')
    {
        DOMEscaper::doEscaping($value, $escape_type);
        $existing_value = $this->nodeValue;

        if ($relative === 'after')
        {
            $value = $existing_value . $value;
        }
        else if ($relative === 'before')
        {
            $value = $value . $existing_value;
        }

        $this->nodeValue = $value;
        return $existing_value;
    }

    /**
     * Remove the node value.
     *
     * @return string The old node value
     */
    public function removeContent()
    {
        $old_value = $this->nodeValue;
        $this->nodeValue = null;
        return $old_value;
    }

    /**
     * Change the node ID.
     *
     * @param string $new_id The new ID
     */
    public function changeId($new_id)
    {
        $this->setAttribute('id', $new_id);
        $this->setIdAttribute('id', true);
    }

    /**
     * Get child element matching the given ID.
     *
     * @param string $id The ID to search for
     * @return DOMElement The first matching element
     */
    public function getElementById($id)
    {
        return DOMHelperFunctions::doXPathQuery($this, "(.//*[@id='" . $id . "'])[1]", $this)->item(0);
    }

    /**
     * Get child elements which contain the given attribute name.
     *
     * @param string $name Name of the attribute
     * @param boolean $as_array If true, function will return an associative array keyed by attribute value
     * @return DOMNodeList A DOMNodeList of matching elements
     */
    public function getElementsByAttributeName($name, $as_array = false)
    {
        $query_result = DOMHelperFunctions::doXPathQuery($this, './/*[@' . $name . ']');

        if($as_array)
        {
            return DOMHelperFunctions::attributeListToArray($query_result, $name);
        }

        return $query_result;
    }

    /**
     * Get child elements which contain the given attribute value.
     *
     * @param string $name Name of the attribute
     * @param string $value Attribute value to match
     * @return DOMNodeList A DOMNodeList of matching elements
     */
    public function getElementsByAttributeValue($name, $value)
    {
        $query_result =  DOMHelperFunctions::doXPathQuery($this, './/*[@' . $name . '=\'' . $value . '\']');
        return $query_result;
    }

    /**
     * Get child elements which contain the given class name.
     *
     * @param string $name Name of the class
     * @return DOMNodeList A DOMNodeList of matching elements
     */
    public function getElementsByClassName($name, $as_array = false)
    {
        $query_result = DOMHelperFunctions::doXPathQuery($this, './/*[@class=\'' . $name . '\']');
        return $query_result;
    }

    /**
     * Get the inner nodes of this element.
     *
     * @param boolean $as_list True to return nodes as a list or false to return a DOMDocument containing the nodes
     * @return DOMNodeList|ExtendedDOMDocument
     */
    public function getInnerNodes($as_list = false)
    {
        $nodes = $this->childNodes;

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

    /**
     * Delete this node.
     */
    public function remove()
    {
        $parent = $this->parentNode;

        if (!is_null($parent))
        {
            $parent->removeChild($this);
        }
        else
        {
            $this->ownerDocument->removeChild($this);
        }
    }

    /**
     * Adds a new child after a reference node
     *
     * @param DOMNode $newnode The new node
     * @param DOMNode $refnode The reference node. If not supplied, newnode is appended to the children.
     * @return DOMNode The inserted node.
     */

    public function insertAfter($newnode, $refnode = null)
    {
        if(is_null($refnode))
        {
            return $this->appendChild($newnode);
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
}