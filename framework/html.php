<?php

class Html {
    private function __construct() {
        // *** Do not touch me *** //
    }
    public static function StartElement($tag, $props, $is_self_closing) {
        if($is_self_closing) {
		    $start = "<".$tag;
		    foreach($props as $key=>$value) {
			    $start .= " ".$key."='".$value."'";
		    }
            $start .= " />";
            return $start;
        }
        else {
            $start = "<".$tag;
            foreach($props as $key=>$value) {
                $start .= " ".$key."='".$value."'";
            }
            $start .= ">";
            return $start;
        }
    }
    public static function Element($tag, $props, $content, $is_self_closing) {
        if($is_self_closing) {
            $start = self::StartElement($tag, $props, true);
            return $start;
        }
        else {
            $start = self::StartElement($tag, $props, false);
            $end = "</".$tag.">";
            return $start . $content . $end;
        }
    }
    public static function Hyperlink($destination, $content, $class='', $id='') {
        $props = array();
	    $props['href'] = $destination;
	    if($class!='') {
		    $props['class'] = $class;
	    }
        if($id!='') {
		    $props['id'] = $id;
	    }
	    return self::Element('a', $props, $content, false);
    }
}