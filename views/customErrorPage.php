<div class="wrap">
    <header>
        <h1><?= esc_html_e('Error: CSRF Token Missing') ;?></h1>
    </header>

    <div>
        <p>
            <?= esc_html_e('We\'re unable to process your request because the CSRF token is missing. This may be due to a session timeout or a security-related matter. Please try submitting the form again.') ;?>
        </p>

        <a href="<?= POEDITOR_PATH;?>" class="button button-primary"><?= esc_html_e('Go back', 'poeditor'); ?></a>
    </div>
</div>