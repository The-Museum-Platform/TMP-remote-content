<div class="wrap">
    <h2>The Museum Platform Remote Content Configuration</h2>
    <?php $plugin_data = get_plugin_data(__DIR__ . '/../tmp-remote-content.php'); ?>

    <form method="POST" action="options.php">
        <?php
        settings_fields('tmp_remote_content_settings');
        do_settings_sections('tmp_remote_content_settings');
        submit_button();
        ?>
    </form>

</div>