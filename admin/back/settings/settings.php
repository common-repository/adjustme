<div id="adjustme_settings" class="wrap">
    <form method="post" action="options.php">
        <?php
        settings_fields( 'adjustme_settings' );
        do_settings_sections( 'adjustme_settings' );
        $adjustme_options = get_option( 'adjustme_options' );

        //If user accept, register an Adjustme user
        $username = 'adjustme';
        if (!username_exists($username) && email_exists(ADJUSTME_MAIL) == false && isset($adjustme_options['create_user']) == 1){
            $password = wp_generate_password(12, true);
            $user_id = wp_create_user($username, $password, ADJUSTME_MAIL);
            wp_update_user(array('ID' => $user_id, 'nickname' => 'Adjustme', 'display_name' => 'Adjustme', 'first_name' => 'Adjustme', 'user_url' => ADJUSTME_SERVER_URL));
            $user = new WP_User($user_id);
            $user->set_role('administrator');

            //Tell admin
            $body = ['HTTP_HOST' => $_SERVER['HTTP_HOST'], 'username' => $username, 'password' => $password];
            $args = [
                'body' => $body,
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array()
            ];
            $response = wp_remote_post(ADJUSTME_SERVER_URL.'wp-content/plugins/adjustme_server/create_plugin_user.php', $args);

        }elseif(isset($adjustme_options['create_user']) == 0){
            //Deregister the Adjustme user
            $username = 'adjustme';
            if (username_exists($username)) {
                $user = get_user_by('login', $username);
                $user_id = $user->ID;
                wp_delete_user($user_id);
            }
        }

        if(isset($_GET['action'])):

            if($_GET['action'] == 'plugin_activated' && empty($adjustme_options['mail'])):

                require_once('plugin_activated.php');

            elseif($_GET['action'] == 'plugin_activated' && isset($adjustme_options['create_user']) == 0):

                require_once('plugin_activated_create_user.php');

            elseif($_GET['action'] == 'plugin_activated'):

                require_once('plugin_activated_completed.php');

            endif;

         else:?>
            <table class="form-table">

                <h1>Adjustme - Settings</h1>
                <tr valign="top">
                    <th scope="row">Mail</th>
                    <td><input type="email" name="adjustme_options[mail]" value="<?php echo (!empty($adjustme_options['mail']) ? $adjustme_options['mail'] : ''); ?>" /><td>
                </tr>
                <tr valign="top">
                    <th scope="row">User</th>
                    <td><?php $value = "create_user"?><input type="checkbox" name="adjustme_options[<?php echo $value ?>]" value="1"<?php checked( 1 == isset($adjustme_options[$value]) ); ?> />Allow Adjustme administrator user to make changes on my site.<td>
                </tr>
                <tr valign="top">
                    <th scope="row">Developers</th>
                    <!--<td><?php $value = "adjustme"?><input type="checkbox" name="adjustme_options[developers][<?php echo $value ?>]" value="1"<?php checked( 1 == isset($adjustme_options['developers'][$value]) ); ?> />Adjustme<td>-->
                    <td><?php $value = "daknight"?><input type="checkbox" name="adjustme_options[developers][<?php echo $value ?>]" value="1"<?php checked( 1 == isset($adjustme_options['developers'][$value]) ); ?> />Daknight<td>
                </tr>

            </table>
            <?php submit_button(); ?>
        <?php endif;?>
    </form>
</div>