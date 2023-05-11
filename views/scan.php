<div class="wrap">
    <h2>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h2>
    <div class="tool-box" style="display: flex">
        <?php echo '<img src="' . plugins_url( '_resources/img/spinner.svg' , __FILE__ ) . '" alt="Loading..." > '; ?>

        <p style="margin-left: 5px;"><?php esc_html_e('Scanning for language files. Please wait', 'poeditor'); ?>&hellip;</p>
    </div>
</div>