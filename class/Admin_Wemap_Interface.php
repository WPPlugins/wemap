<?php
class Admin_Wemap_Interface {
    public function __construct ($version, $name) {
        $this->name = $name;
        $this->version = $version;
        $this->meta_box = new Admin_Wemap_Meta_Box();
        new Shortcode_Wemap();
        add_action('admin_enqueue_scripts', array($this, 'wemap_admin_scripts'));
    }

    public function wemap_admin_scripts() {
        wp_enqueue_style($this->name . '-admin', plugins_url('assets/css/admin.css', dirname(__FILE__)), false, $this->version);
        wp_enqueue_script($this->name . '-pinpoints_post', plugins_url('assets/js/pinpoints_post.js', dirname(__FILE__)), array('jquery'), $this->version);
        wp_enqueue_script($this->name . '-geolocate', plugins_url('assets/js/geolocate.js', dirname(__FILE__)), array('jquery'), $this->version);
        wp_enqueue_script($this->name . '-cookie', plugins_url('assets/js/js.cookie.js', dirname(__FILE__)), array('jquery'), $this->version);
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');
    }
}
?>
