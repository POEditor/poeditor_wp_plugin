<div class="wrap" style="font-family: Arial;">
    <h2>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo_dark.svg' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h2>
    <div class="tool-box" style="display: flex">
        <?php echo '<img src="' . plugins_url( '_resources/img/spinner.svg' , __FILE__ ) . '" alt="Loading..." > '; ?>

        <p style="margin-left: 5px;"><?php esc_html_e('Retrieving online projects from POEditor.com. Please wait', 'poeditor'); ?>&hellip;</p>
    </div>
</div>