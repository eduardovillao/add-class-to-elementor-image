<?php
/**
 * Plugin Name: Add class to Elementor Image
 * Plugin URI: https://eduardovillao.me/wordpress-plugins/
 * Description: Simple plugin to add custom CSS class to Elementor image.
 * Author: EduardoVillao.me
 * Author URI: https://eduardovillao.me/
 * Version: 1.0
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add class to Elementor image
 *
 * @since 1.0
 */
final class ACEI_Init {

	/**
	 * Instance
	 *
	 * @since 1.0
	 *
	 * @access private
	 * @static
	 *
	 * @var ACEI_Init The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @static
	 *
	 * @return ACEI_Init An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
	 * Constructor
	 *
	 * Private method for prevent instance outsite the class.
	 * 
	 * @since 1.0
	 *
	 * @access private
	 */
	private function __construct() {

        add_action( 'plugins_loaded', [ $this, 'add_elementor_mod' ] );
	}

    /**
	 * Init Elemento mods
	 *
	 * Add Elementos mods just is plugins exist and is loaded.
	 * 
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_elementor_mod() {

        if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		add_action( 'elementor/element/image/section_image/before_section_end', [ $this, 'add_class_control' ], 10, 2 );
		add_filter( 'elementor/image_size/get_attachment_image_html', [ $this, 'add_custom_class' ], 10, 4 );
	}

    /**
	 * Add class control
	 *
	 * Add custom control to image widget.
	 * 
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_class_control( $image, $args) {
	
        $image->add_control(
            'cei_image_custom_class',
            [
                'label' => __( 'Custom Class', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'your-custom-class', 'plugin-domain' ),
            ]
        );
    }

    /**
	 * Add custom class
	 *
	 * Add custom class to HTML img.
	 * 
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_custom_class( $html, $settings, $image_size_key, $image_key ) {

        if( $settings['cei_image_custom_class'] ) {
    
            return preg_replace( '/class="(.*)"/', 'class="'.$settings['cei_image_custom_class'].' \1"', $html );
        } 
		else {
			
			return $html;
		}
    }
}

ACEI_Init::instance();