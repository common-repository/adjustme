<?php
function adjustme_init(){
    //We only want admin users to load this. Lucky for us all actions are after init
    if(!current_user_can('activate_plugins')):
        return;
    endif;

    //Get class from functions.php
    $adjustme = new adjustme();

    if ( is_admin() ):

        //Settings
        add_action( 'admin_menu', array($adjustme, 'create_settings_menu') );
        add_action( 'admin_init', array($adjustme, 'register_settings') );

        //Notice
        add_action( 'admin_init', array($adjustme, 'create_notices_if_necessery') );

        //Script and style
        add_action( 'admin_enqueue_scripts', array($adjustme, 'enqueue_admin_script_style') );

        //Add save function to wp_ajax
        add_action( 'wp_ajax_adjustme_save_list', array($adjustme, 'adjustme_save_list') );
        add_action( 'wp_ajax_adjustme_save_pending', array($adjustme, 'adjustme_save_pending') );

        add_filter( 'sanitize_post_meta_adjustme_list', array($adjustme, 'sanitize_meta_adjustme_list') );

    else:

        //Enqueue styles and scripts
        add_action( 'wp_enqueue_scripts', array($adjustme, 'enqueue_required_front_script_style') );
        add_action( 'wp_enqueue_scripts', array($adjustme, 'enqueue_front_script_style') );

        //Add adjustme to admin bar
        add_action( 'admin_bar_menu', array($adjustme, 'create_admin_bar_menu') , 0  );

        //Create front-end vue app
        add_action('wp_head', array($adjustme, 'create_front_output'), 9999);

    endif;
}

add_action('init', 'adjustme_init');

