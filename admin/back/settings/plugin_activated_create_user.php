<div id="plugin_activated" class="adjustme_settings_box">
    <h1>Create Adjustme administrator account</h1>
    <p>For our developers to be able to make the adjustments you request, we need an administrator user account.</p>
    <p>By clicking next we will create and send an administrator login to our secure server. This information will later be used by our developers to login on your site.</p>
    <input type="email" style="visibility: hidden; position:absolute"  name="adjustme_options[mail]" placeholder="Your mail" value="<?php echo (!empty($adjustme_options['mail']) ? $adjustme_options['mail'] : ''); ?>" />
    <?php $value = "create_user"?><input style="visibility: hidden; position:absolute" type="checkbox" name="adjustme_options[<?php echo $value ?>]" value="1" checked required>
    <?php submit_button($text = 'Next'); ?>
</div>