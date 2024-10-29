<?php defined( 'ABSPATH' ) or die( 'Adjustme' );
/*
 * Plugin Name: Adjustme
 * Plugin URI: https://adjustme.io/
 * Description: Make adjustments without coding. With a few clicks you can send your changes to a developer that does the job for you.
 * Author: Martin Lundin
 * Author URI: https://adjustme.io/
 * Version: 1.0.4
 */

define( 'ADJUSTME_VERSION', '1.0.4' );
define( 'ADJUSTME_VERSION_STAMP', (WP_DEBUG ? time() : ADJUSTME_VERSION ) );
define( 'ADJUSTME_PATH', plugin_dir_path(__FILE__) );
define( 'ADJUSTME_URI', plugins_url('', __FILE__) . '/' );
define( 'ADJUSTME_SERVER_URL', 'https://adjustme.io/');
define( 'ADJUSTME_SERVER_AJAX_URL', ADJUSTME_SERVER_URL.'wp-admin/admin-ajax.php' );
define( 'ADJUSTME_MAIL', 'support@adjustme.io');

require_once(ADJUSTME_PATH . 'functions.php');
require_once(ADJUSTME_PATH . 'actions.php');

//Plugin links
add_filter( 'plugin_action_links', function( $actions, $plugin_file ){
    static $plugin;

    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $settings = array('settings' => '<a href="'.admin_url('options-general.php?page=adjustme_settings').'">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="http://adjustme.io" target="_blank">Support</a>');

        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);

    }

    return $actions;
}, 10, 5 );

/* Activation hook */
register_activation_hook(__FILE__, function(){
    add_option('adjustme_activation_redirect', true);
});
add_action('admin_init', function(){
    if (get_option('adjustme_activation_redirect', false)) {
        delete_option('adjustme_activation_redirect');

        //Show admin bar
        update_user_option( get_current_user_id(), 'show_admin_bar_front', "true" );

        wp_redirect(admin_url('options-general.php?page=adjustme_settings&action="plugin_activated"'));
    }
});

/* Deactivation hook */
register_deactivation_hook(__FILE__, function(){
    require_once(ABSPATH.'wp-includes/pluggable.php');
    //Deregister the Adjustme user
    $username = 'adjustme';
    if (username_exists($username)) {
        $user = get_user_by('login', $username);
        $user_id = $user->ID;
        wp_delete_user($user_id);
    }
});
