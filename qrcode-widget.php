<?php
/*
 Plugin Name: Qr Code Adv
 Plugin URI: http://qr-adv.com/qr-code-widget
 Description: A widget for your Wordpress sidebar that displays QR codes
 Version: 1
 Author: Branimir Ivanov
 Author URI: http://bivaga.com
 */

if(!defined('qr_debug'))
    define('qr_debug', 0);
 
require_once('qrcode-widget-core.php');
add_action('widgets_init','widget_ColorWP_qr_init');

// What actions to do when this plugin is activated, deactivated, uninstalled
register_activation_hook( __FILE__, 'colorwp_qr_activate');
register_deactivation_hook(__FILE__, 'colorwp_qr_deactivate');
register_uninstall_hook(__FILE__, 'colorwp_qr_uninstall');

if(is_admin()){
    add_action('admin_enqueue_scripts',"loadQRHelperLibraries");
}

function loadQRHelperLibraries(){
    wp_enqueue_style('color-picker', plugin_dir_url( __FILE__ ) . 'helpers/colorpicker/css/colorpicker.css' );
    wp_enqueue_script('color-picker-qr', plugin_dir_url( __FILE__ ) .'helpers/colorpicker/js/colorpicker.js',array('jquery'));
}


// Could be used in the future
function colorwp_qr_activate(){
}

function colorwp_qr_deactivate(){
}

function colorwp_qr_uninstall(){
}


?>