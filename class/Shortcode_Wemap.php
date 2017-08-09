<?php
class Shortcode_Wemap {
    public function __construct() {
        add_shortcode('livemap', array($this, 'wemap_shortcode_livemaps'));
        add_shortcode('mini_livemap', array($this, 'wemap_shortcode_mini_livemaps'));
    }

    public function wemap_shortcode_livemaps ($atts) {
        extract(shortcode_atts(array(
            'width' => '600',
            'height' => '400',
            'src' => ''
        ), $atts));
        return '<iframe width="' . $width . '" height="' . $height . '" src="'.WEMAP_LIVEMAP_URL.'token=' . $src . '" ></iframe>';
    }
    public function wemap_shortcode_mini_livemaps ($atts) {
        extract(shortcode_atts(array(
            'width' => '600',
            'height' => '400',
            'src' => ''
            ), $atts));
        return '<iframe width="' . $width . '" height="' . $height . '" src="'.WEMAP_LIVEMAP_URL.'ppid=' . $src . '&enablesidebar=false" ' . "allow-script allow-top-navigation allowfullscreen frameborder='0'></iframe>";    
    }
}
?>