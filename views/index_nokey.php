<div class="wrap">
    <h2>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo_dark.svg' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h2>
    <div class="tool-box">
        <h3 class="title"><?php esc_html_e( 'No API key set', 'poeditor' );?></h3>
        <p>
            <?php
            $your_acc_text = esc_html__('your account', 'poeditor');
            $account = '<a href="https://poeditor.com/account/api" target="_blank">' . $your_acc_text . '</a>';
            $app = '<a href="https://poeditor.com/" target="_blank">POEditor.com</a>';
            $text_content = esc_html__('You must set your API key in order to use this plugin. You can get this key by going to %1$s on %2$s', 'poeditor' ); ?>

            <?php echo sprintf($text_content, $account, $app); ?>
        </p>

        <form action="<?php echo POEDITOR_PATH;?>&amp;do=setApiKey" method="post">
            <?php wp_nonce_field('setApiKey_nonce'); ?>

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