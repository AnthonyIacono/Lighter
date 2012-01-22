<?php

class Html {
    public function concat() {
        $args = func_get_args();

        return implode("\n", $args);
    }

    public function element() {
        $args = func_get_args();

        $method = array_shift($args);

        return $this->__call($method, $args);
    }

    public function hyperlink($href, $text, $escape = true, $additional_attributes = array()) {
        return $this->__call('a', array(
            array_merge($additional_attributes, array(
                'href' => $href
            )),
            $escape ? htmlspecialchars($text) : $text
        ));
    }

    public function unordered_list($items, $escape = true, $list_attributes = array(), $item_attributes = array()) {
        $items_html = '';
        foreach($items as $k => $item) {
            $attributes = is_array($item_attributes)
                ? $item_attributes
                : $item_attributes($k);

            $items_html .= $this->li($attributes, $escape ? htmlspecialchars($item) : $item);
        }

        $attributes = is_array($list_attributes)
            ? $list_attributes
            : $list_attributes();

        return $this->ul($attributes, $items_html);
    }

    public function __call($method, $args) {
        $method = strtolower($method); // the method is the element

        if(count($args) == 0) {
            return "<$method />";
        }

        if(is_string($args[0])) {
            array_unshift($args, array());
        }

        $self_closing = !isset($args[1]); // if there is isn't two params, it's self closing

        if(!$self_closing) { // since it's not self closing, capture the second param as the body
            $body = isset($args[2]) && $args[2] // if there is a third param, and it's true, special chars the body
                ? htmlspecialchars($args[1])
                : $args[1];
        }

        $args = is_array($args[0]) // if the first param is an array, use it for attribute arguments. otherwise, use a blank array
                ? $args[0]
                : array();

        // Turn these arguments into an attribute string
        $attr = '';

        foreach($args as $k => $v) {
            $v = htmlspecialchars($v);
            $attr .= " $k=\"$v\"";
        }

        return $self_closing
                ? "<$method$attr />"
                : "<$method$attr>\n$body\n</$method>";
    }
}