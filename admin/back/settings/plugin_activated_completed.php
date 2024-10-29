<?php
$body = ['domain' => $_SERVER['HTTP_HOST'], 'mail' => $adjustme_options['mail']];
$args = [
    'body' => $body,
    'timeout' => '5',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => array(),
    'cookies' => array()
];
$response = wp_remote_post(ADJUSTME_SERVER_URL.'wp-content/plugins/adjustme_server/create_user.php', $args);
?>
<div id="plugin_activated_completed" class="adjustme_settings_box">
    <h1>That's it!</h1>
    <p>Adjustme is simple. This is how you make the adjustments.<p>
    <ol>
        <li>Go to the front-end of the page or post you want to adjust</li>
        <li>Click the "Adjustme" button in the upper left corner, point and click on where you want the adjustment<br><img src="<?php echo ADJUSTME_URI?>admin/back/images/first.png"></li>
        <li>Write a short description of what adjustment you want<br><img src="<?php echo ADJUSTME_URI?>admin/back/images/second.png"></li>
        <li>Add more adjustments or when you are finnished, click "Send to developer"<br><img src="<?php echo ADJUSTME_URI?>admin/back/images/third.png"></li>
    </ol>
    <p class="submit">
        <a href="<?php echo get_home_url()?>" class="button button-primary text-right">Go to front page</a>
    </p>
</div>