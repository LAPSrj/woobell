<?php
/*
	Plugin Name: WooBell
	Description: Rings a bell every time a new order arrives
	Version: 1.0
	Author: Leandro Amorim
	Author URI: https://github.com/LAPSrj
    Text Domain: woobell
    Domain Path: /languages
*/

function woobell_assets() {
    wp_enqueue_style( 'woobell_css', plugins_url('/woobell.css', __FILE__), false);
	wp_enqueue_script( 'woobell_js', plugins_url('/woobell.js', __FILE__), array('jquery') );
    $vars = array(
        'ajax_url' => admin_url('admin-ajax.php'), 
        'sound_url' => plugins_url('/ring.mp3', __FILE__),
        'admin_url' => admin_url('post.php'),
    );
    wp_localize_script('woobell_js', 'woobell', $vars);
}

function woobell_last_order(){
    $args = array(
        'limit' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'ids',
    );

    if(!empty($_REQUEST['processing'])){
        $args['status'] = array('wc-processing');
    }

    $orders = wc_get_orders($args);

    if(!empty($orders[0])){
        echo $orders[0];
    }

    wp_die();
}

function woobell_html(){
    include plugin_dir_path(__FILE__) . 'notification.php';
}

function woobell_assets_init(){
    if(!current_user_can('edit_shop_orders')) return false;

    load_plugin_textdomain('woobell', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	add_action('admin_enqueue_scripts', 'woobell_assets');
    add_action('wp_ajax_woobell_last_order', 'woobell_last_order');
    add_action('wp_ajax_nopriv_woobell_last_order', 'woobell_last_order');
    add_action('admin_footer', 'woobell_html');
}

add_action('init', 'woobell_assets_init');