<div id="plugin_activated" class="adjustme_settings_box">
    <h1>Get started with Adjustme</h1>
    <p>We will create an account for you at our website (http://adjustme.io). Just enter you email and we will send a mail with further instructions.</p>
    <input type="email" name="adjustme_options[mail]" placeholder="Your mail" value="<?php echo (!empty($adjustme_options['mail']) ? $adjustme_options['mail'] : ''); ?>" />
    <?php submit_button($text = 'Next'); ?>
</div>