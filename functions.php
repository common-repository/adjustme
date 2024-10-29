<?php
if ( !class_exists( 'adjustme' )){
    class adjustme{

        public function enqueue_admin_script_style(){
            wp_enqueue_script('adjustme_admin_back_script', ADJUSTME_URI.'admin/back/js/adjustme_admin_back.js', array(), ADJUSTME_VERSION_STAMP, true);
            wp_enqueue_style('adjustme_admin_back_style', ADJUSTME_URI.'admin/back/css/adjustme_admin_back.css', array(), ADJUSTME_VERSION_STAMP);
        }

        public function enqueue_required_front_script_style(){
            wp_enqueue_style('font-awesome', ADJUSTME_URI.'admin/front/css/font-awesome-4.7.0/css/font-awesome.min.css');
            wp_enqueue_script('jquery');
            wp_enqueue_script('vuejs', ADJUSTME_URI.'admin/front/js/vue.min.js', array(), '2.4.2', true);
            wp_enqueue_script('jquery-debounce', ADJUSTME_URI.'admin/front/js/jquery.ba-throttle-debounce.min.js', array(), '1.1', true);
        }

        public function enqueue_front_script_style(){
            wp_enqueue_script('adjustme_admin_front_script', ADJUSTME_URI.'admin/front/js/adjustme_admin_front.js', array(), ADJUSTME_VERSION_STAMP, true);
            wp_enqueue_style('adjustme_admin_front_style', ADJUSTME_URI.'admin/front/css/adjustme_admin_front.css', array(), ADJUSTME_VERSION_STAMP);

            global $post;
            $post->post_meta = get_post_meta($post->ID);
            wp_localize_script('adjustme_admin_front_script', 'adjustme_vars', array(
                    'debug' => WP_DEBUG,
                    'post' => $post,
                    'server_url' => ADJUSTME_SERVER_AJAX_URL,
                    'settings' => get_option('adjustme_options')
                )
            );
        }

        public function create_settings_menu(){
            add_options_page(
                'Adjustme Settings',
                'Adjustme',
                'manage_options',
                'adjustme_settings',
                 function(){
                    include_once(ADJUSTME_PATH.'/admin/back/settings/settings.php');
                }
            );
        }

        public function register_settings() {
            register_setting( 'adjustme_settings', 'adjustme_options' );
            register_setting( 'adjustme_settings', 'adjustme_user' );
        }

        public function create_admin_bar_menu(\WP_Admin_Bar $bar){
            if (is_admin_bar_showing() && !is_admin()):

                $bar->add_menu(array(
                    'id' => 'adjustme',
                    'title' => '<span class="ab-icon"></span><span class="ab-label">Adjustme</span>',
                    'href' => '#adjustme',
                    'meta' => array(
                        'tabindex' => PHP_INT_MAX,
                        'onclick' => 'adjustme.activation()'
                    )
                ));

            endif;
        }

        private function create_admin_notice($message, $type="notice-info"){
            add_action( 'admin_notices', function() use($message, $type){
                //"The class of admin notice. Should be notice plus any one of notice-error, notice-warning, notice-success, or notice-info. Optionally use is-dismissible to apply a closing icon."
                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice ' . $type ), $message );
            });
        }

        public function create_notices_if_necessery(){
            if(get_user_option("show_admin_bar_front", get_current_user_id()) == "false" && is_admin()):
                $this->create_admin_notice('The plugin "Adjustme" requires the Toolbar. You can find and activate it <a href="'.admin_url().'profile.php">here</a>', 'notice-error');
            endif;
        }

        public function create_front_output(){
            require_once(ADJUSTME_PATH.'admin/front/output.php');
        }

        public function adjustme_save_list(){
            $meta_key = 'adjustme_list';
            $post = (object) $_POST['post'];
            $data = $_POST['data'];

            $meta_value = sanitize_meta($meta_key, $data, 'post');

            add_post_meta($post->ID, $meta_key, $meta_value, true);
            update_post_meta($post->ID, $meta_key, $meta_value);
            echo get_post_meta($post->ID, $meta_key, true);
            die();
        }

        public function adjustme_save_pending(){
            $meta_key = 'adjustme_pending';
            $post = (object) $_POST['post'];
            $data = $_POST['data'];

            $meta_value = sanitize_meta($meta_key, $data, 'post');

            add_post_meta($post->ID, $meta_key, $meta_value, true);
            update_post_meta($post->ID, $meta_key, $meta_value);
            echo get_post_meta($post->ID, $meta_key, true);
            die();
        }

        public function sanitize_meta_adjustme_list($input){
            if(is_string($input)){
                return $input;
            }else{
                wp_die('Invalid');
            }
        }

    }
}