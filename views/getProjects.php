<div class="wrap" style="font-family: Arial;">
    <h2>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h2>
    <div class="tool-box">
        <div class="icon16">
            <?php
            echo '<img src="' . plugins_url( '_resources/img/preloader.gif' , __FILE__ ) . '" alt="'.__('Loading', 'poeditor').'&hellip;" > ';
            ?>
        </div>
        <p><?php esc_html_e('Retrieving online projects from POEditor.com. Please wait', 'poeditor'); ?>&hellip;</p>
    </div>
</div>