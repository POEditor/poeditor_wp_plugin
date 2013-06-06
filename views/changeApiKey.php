<div class="wrap">
	<h2>
		<?php
			echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
		?>
	</h2>
	<div class="tool-box">
		<h3 class="title"><?php _e( 'Change your POEditor API Key', 'poeditor' );?></h3>
		
		<form action="<?php echo POEDITOR_PATH;?>&amp;do=setApiKey" method="post">
			<p>
				<label for="apikey"><?php _e( 'POEditor API KEY', 'poeditor' );?>:</label>
				<input type="text" name="apikey" id="apikey" class="regular-text" value="<?php echo $this->apiKey;?>" />
			</p>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Change API Key', 'poeditor'); ?>">
			</p>
		</form>
	</div>
</div>