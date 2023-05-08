<div class="wrap">
    <h2>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h2>
    <div class="tool-box">
        <h3 class="title"><?php esc_html_e( 'No API key set', 'poeditor' );?></h3>
        <p>
            <?php $text_content = printf(__( 'You must set your API key in order to use this plugin. You can get this key by going to %1$s on %2$s', 'poeditor' )); ?>
            <?php esc_html_e($text_content) . '<a href="https://poeditor.com/account/api" target="_blank">'. esc_html_e('your account', 'poeditor').'</a>' . '<a href="https://poeditor.com/" target="_blank">POEditor.com</a>';?>.
        </p>
        <form action="<?php echo POEDITOR_PATH;?>&amp;do=setApiKey" method="post">
            <p>
                <label for="apikey"><?php esc_html_e( 'POEditor API KEY', 'poeditor' );?>:</label>
                <input type="text" name="apikey" id="apikey" class="regular-text" />
            </p>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Set API Key', 'poeditor'); ?>">
            </p>
        </form>
    </div>
</div>