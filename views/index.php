<?php
	wp_enqueue_script( 'jquery' );
	wp_register_style( 'poeditor-style', plugins_url( '_resources/style.css' , __FILE__ ), array(), '20120208', 'all' );
	wp_enqueue_style( 'poeditor-style' );
?>
<div class="wrap poeditor">
	<div id="poeditorTopLinks">
		<a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=changeApiKey"><?php _e( 'Change API Key', 'poeditor' ); ?></a>
		<a class="button-secondary poeditorReset" href="#reset" title="<?php _e( 'Disconnect plugin from linked POEditor account', 'poeditor' ); ?>"><?php _e( 'Reset plugin', 'poeditor' ); ?></a>
	</div>
	<h1>
		<?php
			echo '<img src="' . plugins_url( '_resources/img/logo.png' , __FILE__ ) . '" alt="POEditor" > ';
		?>
	</h1>
	<br clear="all">
	<a class="button-secondary poeditorTableExtraLink" href="<?php echo POEDITOR_PATH;?>&amp;do=getProjects" title="<?php _e( 'Update list of POEditor translation projects', 'poeditor' ); ?>">
		<span class="buttons-icon-refresh"></span>
		<?php _e( 'Refresh online projects list', 'poeditor' ); ?>
	</a>
	<h2 class="title poeditorTableTitle">
		<?php _e('POEditor translations', 'poeditor'); ?>
	</h2>

	<br clear="all">
	<?php
	if( is_array($projects) && !empty( $projects) ) {
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th>
						<?php _e('Project', 'poeditor'); ?>
					</th>
					<th width="200">
						<?php _e('Language', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Progress', 'poeditor'); ?>
					</th>
					<th class="poeditorPadLeft">
						<?php _e('Assigned file', 'poeditor'); ?>
					</th>
					<th class="poeditorToRight">
						<?php _e('Actions', 'poeditor'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i = 1;
					$j = 0;

					foreach ($projects as $project) {
						?>
						<tr <?php if( $i % 2 == 0 ) echo 'class="alternate"';?>>
							<td><?php echo $project['name'];?></td>
							<td><?php echo $project['code'] ? $project['language'].' ('.$project['code'].')' : "";?></td>
							<td><?php echo $project['code'] ? $project['percentage'] . "%" : '';?></td>
							<td class="poeditorPadLeft">
								<?php
									$key = $project['id'] . '_' . $project['code'];
									if($project['code']){
										if( isset($assingments[$key])) {
											echo str_replace(WP_CONTENT_DIR, '', $assingments[$key]);
										} else {
											?>
											<a href="#assignFile" project="<?php echo $project['id'];?>" projectName="<?php echo $project['name'];?>" language="<?php echo $project['code'];?>" class="assignFile"><?php _e('Assign file', 'poeditor'); ?></a>
											<?php
										}
									}
								?>
							</td>
							<td class="poeditorToRight">
								<?php
									if( isset($assingments[$key]) ) {
										?>
										<a href="<?php echo POEDITOR_PATH;?>&amp;do=import&amp;projectId=<?php echo $project['id'];?>&amp;language=<?php echo $project['code'];?>" title="<?php _e('Import .po and .mo files from POEditor', 'poeditor'); ?>"><?php _e('Import', 'poeditor'); ?></a> | 
										<a href="<?php echo POEDITOR_PATH;?>&amp;do=export&amp;projectId=<?php echo $project['id'];?>&amp;language=<?php echo $project['code'];?>&amp;type=export" title="<?php _e('Export terms to POEditor', 'poeditor'); ?>"><?php _e('Export', 'poeditor'); ?></a> |
										<a href="<?php echo POEDITOR_PATH;?>&amp;do=export&amp;projectId=<?php echo $project['id'];?>&amp;language=<?php echo $project['code'];?>&amp;type=sync" title="<?php _e('Export terms and translations to POEditor, overwriting exiting translations', 'poeditor'); ?>"><?php _e('Sync', 'poeditor'); ?></a> | 
										<a href="<?php echo POEDITOR_PATH;?>&amp;do=unassignFile&amp;projectId=<?php echo $project['id'];?>&amp;language=<?php echo $project['code'];?>" title=""><?php _e('Unassign file', 'poeditor'); ?></a>
										<?php
									}
								?>
							</td>
						</tr>
						<?php
						if( !isset($projects[$j+1]['id']) || $project['id'] != $projects[$j+1]['id'] ) {
							?>
							<tr>
								
								<td colspan="2">
									<a href="#addLanguage" class="addLanguageButton button-secondary" rel="<?php echo $project['id'];?>">+ <?php printf(__('Add language to %s', 'poeditor'), '"'.$project['name'].'"' );?></a>
									<form action="<?php echo POEDITOR_PATH;?>&amp;do=addLanguage" class="addLanguage" id="addLanguage_<?php echo $project['id'];?>" method="post">
										<select name="language">
											<?php
												foreach ($languages as $code => $language) {
													?>
													<option value="<?php echo $code;?>"><?php echo $language;?></option>
													<?php
												}
											?>
										</select>
										<input type="hidden" value="<?php echo $project['id'];?>" name="project">
										<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Add language', 'poeditor'); ?>"> 
										<a href="#" class="cancelAddLanguage" rel="<?php echo $project['id'];?>"><?php _e('Cancel', 'poeditor'); ?></a>
									</form>
								</td>
								<td ></td>
								<td ></td>
								<td class="poeditorToRight">
								<?php if($project['code']) { ?>
								<a onclick='return confirm("<?php printf(__('Do you really like to download all language files for %s from POEditor.com?', 'poeditor'), $project['name']); ?>");' href="<?php echo POEDITOR_PATH;?>&amp;do=import_all&amp;projectId=<?php echo $project['id'];?>" title="<?php _e('Import .po and .mo files from POEditor for all languages', 'poeditor'); ?>"><?php _e('Import all', 'poeditor'); ?></a> | 
								<a onclick='return confirm("<?php printf(__('Do you really upload all terms for %s to POEditor.com?', 'poeditor'), $project['name']); ?>");' href="<?php echo POEDITOR_PATH;?>&amp;do=export_all&amp;projectId=<?php echo $project['id'];?>&amp;type=export" title="<?php _e('Export terms to POEditor from all language files', 'poeditor'); ?>"><?php _e('Export all', 'poeditor'); ?></a> |
								<a onclick='return confirm("<?php printf(__('Do you really upload all definitions for %s POEditor.com?', 'poeditor'), $project['name']); ?>");' href="<?php echo POEDITOR_PATH;?>&amp;do=export_all&amp;projectId=<?php echo $project['id'];?>&amp;type=sync" title="<?php _e('Export terms and translations to POEditor from all language files, overwriting exiting translations', 'poeditor'); ?>"><?php _e('Sync all', 'poeditor'); ?></a>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="5" class="poeditorProjectOptions">&nbsp;</td>
							</tr>
							<?php
						}
						$i++;
						$j++;
					}
				?>
				<tr>
					<td colspan="5">
						<a href="#addProject" class="addProjectButton button-secondary">+ <?php _e('Create project', 'poeditor'); ?></a>
						<form action="<?php echo POEDITOR_PATH;?>&amp;do=addProject" class="addProject" method="post">
							<input type="text" name="project" id="projectNameInput">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Create project', 'poeditor'); ?>">
						</form>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th>
						<?php _e('Project', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Language', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Progress', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Assigned file', 'poeditor'); ?>
					</th>
					<th class="poeditorToRight">
						<?php _e('Actions', 'poeditor'); ?>
					</th>
				</tr>
			</tfoot>
		</table>
		<?php
	} else {
		?>
		<p><?php _e('Found no projects in your POEditor.com account.', 'poeditor'); ?></p>
		
		<a href="#addProject" class="addProjectButton button-primary">+ <?php _e('Create project', 'poeditor'); ?></a>
		<form action="<?php echo POEDITOR_PATH;?>&amp;do=addProject" class="addProject" method="post">
			<input type="text" name="project" id="projectNameInput">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Create project', 'poeditor'); ?>">
		</form>
		<?php
	}
	?>
	
	<h2 class="title poeditorTableTitle">
		<?php _e('Local language files', 'poeditor'); ?>
	</h2>
	<a class="button-secondary poeditorTableExtraLink" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php _e( 'Search for local .po and .pot files', 'poeditor' ); ?>">
		<span class="buttons-icon-refresh"></span>
		<?php _e( 'Rescan for language files', 'poeditor' ); ?>
	</a>
	<?php
	if( is_array($locations) && !empty( $locations) ) {
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th>
						<?php _e('Location', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('File', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Last changed', 'poeditor'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i = 1;
					foreach ($locations as $folder => $files) {
						$j = 1;
						$totalFiles = count($files);
						foreach ($files as $file) {
							?>
							<tr <?php if( $i % 2 == 0 ) echo 'class="alternate"';?>>
								<?php if( $j == 1 ) {
											echo '<td rowspan="' . ($totalFiles) .  '" valign="top" class="poeditorVerticalAlign">';
											echo $folder;

											if( !is_writable(WP_CONTENT_DIR . $folder) ) {
												?>
												<img src="<?php echo plugins_url( '_resources/img/warning.png' , __FILE__ );?>" class="poeditorWarningIcon" alt="This folder is not writable">
												<?php
											}
										}

									?>
								<?php if( $j == 1 ) echo '</td>';?>
								<td>
									<?php 
										echo $file;

										if( !is_writable(WP_CONTENT_DIR . $folder . $file) ) {
											?>
											<img src="<?php echo plugins_url( '_resources/img/warning.png' , __FILE__ );?>" class="poeditorWarningIcon" alt="This folder is not writable">
											<?php
										}
									?>
								</td>
								<td>
									<?php
										$filemtime = filemtime(WP_CONTENT_DIR . $folder . $file);
										echo date(get_option('date_format') . ', ' . get_option('time_format'), $filemtime);
									?>
								</td>
							</tr>
							<?php
							$i++;
							$j++;
						}
						
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>
						<?php _e('Location', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('File', 'poeditor'); ?>
					</th>
					<th>
						<?php _e('Last changed', 'poeditor'); ?>
					</th>
				</tr>
			</tfoot>
		</table>
		<?php
	} else {
		?>
		<br clear="both" />
		<a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php _e( 'No language files found yet. Scan now', 'poeditor' ); ?>"><?php _e( 'No language files found yet. Scan now', 'poeditor' ); ?></a>
		<?php
	}
	?>
	<div id="assignFile">
		<input type="hidden" name="project" id="assignFileProjectId" value="0">
		<input type="hidden" name="language" id="assignFileLanguageCode" value="">
		<h2 class="title">
			<?php _e('Assign a local file to a POEditor project language', 'poeditor'); ?> - <span id="assignFileProjectName"></span>
		</h2>
		<?php
		if( is_array($locations) && !empty( $locations) ) {
			?>
			<table class="widefat">
				<tr>
					<th>
						#
					</th>
					<th width="45%">
						<?php _e('Location', 'poeditor'); ?>
					</th>
					<th width="45%">
						<?php _e('File', 'poeditor'); ?>
					</th>
					<th>
					</th>
				</tr>
				<tr>
					<td></td>
					<td><input type="text" id="location_search" class="file-search" placeholder="<?php _e('Search for location', 'poeditor'); ?>"></td>
					<td><input type="text" id="file_search" class="file-search" placeholder="<?php _e('Search for file name', 'poeditor'); ?>"></td>
					<td></th>
				</tr>
				<?php
					$i = 1;
					foreach ($locations as $folder => $files) {
						$j = 1;
						$totalFiles = count($files);
						foreach ($files as $file) {
							?>
							<tr class="search-row<?php if( $i % 2 == 0 ) echo  ' alternate';?>">
								<td><?php echo $i;?></td>
								<td valign="top" class="location-file">
									<?php 
										echo $folder.'<span class="hidden">'.strtolower($folder).'</span>';
									?>
								</td>
								<td class="name-file"><?php echo $file.'<span class="hidden">'.strtolower($file).'</span>';?></td>
								<td>
									<a class="button-secondary hasPath selectPath" rel="<?php echo base64_encode(WP_CONTENT_DIR.$folder.$file);?>" href="#select" title="<?php _e( 'Select', 'poeditor' ); ?>"><?php _e( 'Select', 'poeditor' ); ?></a>
								</td>
							</tr>
							<?php
							if( $j == $totalFiles ) {
								?>
								<tr class="search-row<?php if( $i % 2 == 0 ) echo  ' alternate';?>">
									<td><?php echo ++$i;?></td>
									<td valign="top" class="location-file">
										<?php 
										echo $folder.'<span class="hidden">'.strtolower($folder).'</span>';
										?>
									</td>
									<td>
										<?php _e('Add new', 'poeditor');?>: 
										<input type="text" placeholder="filename.po" name="newFilename" class="all-options" id="addNewSelect_<?php echo $i . '_' . $j;?>">
									</td>
									<td>
										<a class="button-secondary selectPath" folder="<?php echo WP_CONTENT_DIR.$folder;?>" rel="addNewSelect_<?php echo $i . '_' . $j;?>" href="#select" title="<?php _e( 'Select', 'poeditor' ); ?>"><?php _e( 'Select', 'poeditor' ); ?></a>
									</td>
								</tr>
								<tr class="search-row<?php if( $i % 2 == 0 ) echo  ' alternate';?>"><td colspan="4">&nbsp;</td></tr>
								<?php
							} 
							$i++;
							$j++;
						}
						
					}
				?>
					<tr>
						<td colspan="3">
							<?php _e('Add location manually', 'poeditor' );?>: 
							<input type="text" name="newFilename" class="regular-text" id="addNewSelect_0_0">
						</td>
						<td>
							<a class="button-secondary selectPath" rel="addNewSelect_0_0" folder="<?php echo WP_CONTENT_DIR;?>" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php _e( 'Select', 'poeditor' ); ?>"><?php _e( 'Select', 'poeditor' ); ?></a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<small>
								<?php _e('Example', 'poeditor'); ?>: <i>/themes/twentyeleven/languages/test.po</i>
							</small>
						</td>
					</tr>
			</table>
			<?php
		} else {
			?>
			<table>
				<tr>
					<td colspan="3">
						<?php _e('Add location manually', 'poeditor' );?>: 
						<input type="text" name="newFilename" class="regular-text" id="addNewSelect_0_0">
					</td>
					<td>
						<a class="button-secondary selectPath" rel="addNewSelect_0_0" folder="<?php echo WP_CONTENT_DIR;?>" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php _e( 'Select', 'poeditor' ); ?>"><?php _e( 'Select', 'poeditor' ); ?></a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<small>
							<?php _e('Example', 'poeditor'); ?>: <i>/themes/twentyeleven/languages/test.po</i>
						</small>
					</td>
				</tr>
			</table>
			<a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php _e( 'No language files found yet. Scan now', 'poeditor' ); ?>"><?php _e( 'No language files found yet. Scan now', 'poeditor' ); ?></a>
			<?php
		}
		?>
		<a href="#cancel" class="button button-primary" id="cancelFileAssign"><?php _e('Cancel', 'poeditor'); ?></a>
	</div>
	<p>
		<img src="<?php echo plugins_url( '_resources/img/warning.png' , __FILE__ );?>" class="poeditorWarningIcon" alt="<?php _e('This folder is not writable', 'poeditor'); ?>"> = <?php _e('The folder or file is not writable (so we are not be able to update the files with the information from poeditor.com)', 'poeditor'); ?>
	</p>

	<div id="resetConfirm">
		<h4>
			<?php _e('Are you sure you want to reset the plugin?', 'poeditor'); ?>
		</h4>
		<p>
			<?php printf(__('This will delete all your local file assignments and it will detach your Wordpress installation from you account on %s', 'poeditor'),'POEditor.com'); ?>
		</p>
		<a href="#cancel" class="button button-primary" id="poeditorCancelReset"><?php _e('Cancel', 'poeditor'); ?></a>
		<a href="<?php echo POEDITOR_PATH;?>&amp;do=clean" class="button button-primary" id="poeditorProceedWithReset"><?php _e('Reset', 'poeditor'); ?></a>
	</div>
</div>
<script src="<?php echo plugins_url( '_resources/js/jquery.base64.min.js' , __FILE__ );?>"></script>
<script>

	jQuery('.addLanguageButton').on('click', function(e){
		var projectId;

		projectId = jQuery(this).attr('rel');
		jQuery(this).hide();
		jQuery('#addLanguage_' + projectId).show();

		e.preventDefault();
	});

	jQuery('.cancelAddLanguage').on('click', function(e){
		var projectId;

		projectId = jQuery(this).attr('rel');
		jQuery('#addLanguage_' + projectId).hide();
		jQuery('.addLanguageButton').show();
		e.preventDefault();
	});

	jQuery('.addProjectButton').on('click', function(e){
		jQuery(this).hide();
		jQuery('.addProject').show();
		jQuery('#projectNameInput').focus();
		e.preventDefault();
	});

	jQuery('.selectPath').on('click', function(e){
		var projectId, language, path, path_raw, identifier, folder;

		projectId = jQuery("#assignFileProjectId").val();
		language = jQuery("#assignFileLanguageCode").val();

		if( jQuery(this).hasClass('hasPath') ) {
			path = jQuery(this).attr('rel');
		} else {
			identifier = jQuery(this).attr('rel');
			path_raw = jQuery("#" + identifier).val();

			if( path_raw == '' ) {
				jQuery("#" + identifier).addClass('error');
				return false;
			}

			folder = jQuery(this).attr('folder');
			console.log(jQuery.base64);
			console.log(folder);
			console.log(path_raw);
			path = jQuery.base64.encode(folder + path_raw);
		}

		window.location = '<?php echo POEDITOR_PATH;?>&do=assignFile&project=' + projectId + '&language='+language+'&path=' + path; 

		e.preventDefault();
	});

	jQuery('.assignFile').on('click', function(e){
		var projectId, projectName, language;

		projectId = jQuery(this).attr('project');
		projectName = jQuery(this).attr('projectName');
		language = jQuery(this).attr('language');
		jQuery("#assignFileProjectId").val(projectId);
		jQuery("#assignFileProjectName").html(projectName + ' ['+language+']');
		jQuery("#assignFileLanguageCode").val(language);

		jQuery("div#assignFile").fadeIn();
		jQuery('#location_search').select().focus();
		
		e.preventDefault();
	});

	jQuery('#cancelFileAssign').on('click', function(e){
		
		jQuery("#assignFileProjectId").val(0);
		jQuery("#assignFileLanguageCode").val('');

		jQuery("div#assignFile").fadeOut();
		e.preventDefault();
	});

	jQuery('.poeditorReset').on('click', function(e){
		
		jQuery("div#resetConfirm").fadeIn();
		e.preventDefault();
	});

	jQuery('#poeditorCancelReset').on('click', function(e){
		jQuery("div#resetConfirm").fadeOut();
		e.preventDefault();
	});
	
	jQuery('.file-search').on('keyup', function(){
		var _this = jQuery(this),
			val = _this.val().toLowerCase(),
			selector = _this.is('#location_search') ? 'td.location-file' : 'td.name-file';
			
		if(val == ''){
			jQuery('tr.search-row').show();
		}else{
			jQuery('tr.search-row').hide();
			var _el = jQuery(selector+":contains('"+val+"')");
			
			_el.parent().show();
		}
		
		
	});
</script>