<div class="wrap">
	<h2>
		<?php
			echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
		?>
	</h2>
	<div class="tool-box">
		<h3 class="title"><?php _e( 'No API key set', 'poeditor' );?></h3>
		<p>
			<?php printf(__( 'You must set your API key in order to use this plugin. You can get this key by going to %1$s on %2$s', 'poeditor' ), '<a href="https://poeditor.com/account/api" target="_blank">'.__('your account', 'poeditor').'</a>', '<a href="https://poeditor.com/" target="_blank">POEditor.com</a>');?>.
		</p>
		<form action="<?php echo POEDITOR_PATH;?>&amp;do=setApiKey" method="post">
			<p>
				<label for="apikey"><?php _e( 'POEditor API KEY', 'poeditor' );?>:</label>
				<input type="text" name="apikey" id="apikey" class="regular-text" />
			</p>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Set API Key', 'poeditor'); ?>">
			</p>
		</form>
	</div>
</div>