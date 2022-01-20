<?php

/**
* Plugin Name: Webp Convertto
* Plugin URI: https://github.com/thgodamn/webp_convertto
* Description: Конвертируй все изображения в webp без головной боли
* Version: 1.0.0
* Author: MS (Magomed Saybulaev)
* Author URI: https://github.com/thgodamn
* License: GPL2
*/

#namespace Webp_Convertto;

class Webp_Convertto
{
	private static $instance;
    
    private function __construct()
    {
        register_activation_hook( __FILE__, 'webp_convertto_activation' );
		register_deactivation_hook( __FILE__, 'webp_convertto_deactivation' );
		add_action('admin_menu', array( $this, 'webp_convertto_menu' ), 90);
		add_action('after_setup_theme', array( $this, 'webp_after_setup_theme' ), 90);
    }

	public function webp_convertto_menu() {
		add_menu_page(  $this->plugin_name, 'Webp Convertto', 'administrator', 'webp-convertto/webp_convertto_settings.php', '', 'dashicons-format-image', 26 );
	}
	
	public function webp_after_setup_theme() {
		add_filter( 'wp_get_attachment_image_src', array( __CLASS__, 'replace_imgto_webp'), 91, 4 );
	}
	
	public function replace_imgto_webp( $image, $attachment_id, $size, $icon ){
		if (file_exists(ABSPATH . substr(wp_make_link_relative( preg_replace( '(.png|.jpg|.jpeg)', '.webp', $image[0] ) ),1)))
			$image[0] = preg_replace( '(.png|.jpg|.jpeg)', '.webp', $image[0] );
		return $image;
	}
    
	//activation hook
    public function webp_convertto_activation()
    {
    }

    // Deactivation hook
    public function webp_convertto_deactivation()
    {
    }

    public static function getInstance()
    {
        return self::$instance !== null ? self::$instance : new self();
    }
}

Webp_Convertto::getInstance();


?>