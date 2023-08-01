<?php
wp_enqueue_script( 'jquery' );
wp_register_style( 'poeditor-style', plugins_url( '_resources/style.css' , __FILE__ ), array(), '20120208', 'all' );
wp_enqueue_style( 'poeditor-style' );
?>
<div class="wrap poeditor">
    <div id="poeditorTopLinks">
        <a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=changeApiKey"><?php esc_html_e( 'Change API Key', 'poeditor' ); ?></a>
        <a class="button-secondary poeditorReset" href="#reset" title="<?php esc_attr_e( 'Disconnect plugin from linked POEditor account', 'poeditor' ); ?>"><?php esc_html_e( 'Reset plugin', 'poeditor' ); ?></a>
    </div>
    <h1>
        <?php
        echo '<img src="' . plugins_url( '_resources/img/logo_dark.svg' , __FILE__ ) . '" alt="POEditor" > ';
        ?>
    </h1>
    <br clear="all">
    <a class="button-secondary poeditorTableExtraLink" href="<?php echo POEDITOR_PATH;?>&amp;do=getProjects" title="<?php esc_attr_e( 'Update list of POEditor translation projects', 'poeditor' ); ?>">
        <span class="buttons-icon-refresh"></span>
        <?php esc_html_e( 'Refresh online projects list', 'poeditor' ); ?>
    </a>
    <h2 class="title poeditorTableTitle">
        <?php esc_html_e('POEditor translations', 'poeditor'); ?>
    </h2>

    <br clear="all">
    <?php
    if( is_array($projects) && !empty( $projects) ) {
        ?>
        <table class="widefat">
            <thead>
            <tr>
                <th>
                    <?php esc_html_e('Project', 'poeditor'); ?>
                </th>
                <th width="200">
                    <?php esc_html_e('Language', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Progress', 'poeditor'); ?>
                </th>
                <th class="poeditorPadLeft">
                    <?php esc_html_e('Assigned file', 'poeditor'); ?>
                </th>
                <th class="poeditorToRight">
                    <?php esc_html_e('Actions', 'poeditor'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            $j = 0;
            $links = 0;

            foreach ($projects as $project) {
                ?>
                <tr <?php if( $i % 2 == 0 ) echo 'class="alternate"';?>>
                    <td><?php echo esc_html($project['name']);?></td>
                    <td><?php echo esc_html($project['code'] ? $project['language'].' ('.$project['code'].')' : "");?></td>
                    <td><?php echo esc_html($project['code'] ? $project['percentage'] . "%" : '');?></td>
                    <td class="poeditorPadLeft" id="project-<?php echo esc_attr($project['id']); ?>">
                        <?php
                        $key = $project['id'] . '_' . $project['code'];
                        if($project['code']){
                            if( isset($assingments[$key])) {
                                echo str_replace(WP_CONTENT_DIR, '', $assingments[$key]);
                            } else {
                                ?>
                                <a href="#assignFile" project="<?php echo esc_attr($project['id']);?>"
                                   projectName="<?php echo esc_attr($project['name']);?>"
                                   language="<?php echo esc_attr($project['code']);?>" class="assignFile">
                                    <?php esc_html_e('Assign file', 'poeditor'); ?></a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <td class="poeditorToRight">
                        <?php
                        if( isset($assingments[$key]) ) {
                            $links = 1;
                            ?>
                            <a href="<?php echo POEDITOR_PATH;?>&amp;do=import&amp;
										projectId=<?php echo esc_attr($project['id']);?>&amp;
										language=<?php echo esc_attr($project['code']);?>"
                               title="<?php esc_attr_e('Import .po and .mo files from POEditor', 'poeditor'); ?>">
                                <?php esc_html_e('Import', 'poeditor'); ?></a> |

                            <a href="<?php echo POEDITOR_PATH;?>&amp;do=export&amp;
                                        projectId=<?php echo esc_attr($project['id']);?>&amp;
                                        language=<?php echo esc_attr($project['code']);?>&amp;type=export"
                               title="<?php esc_attr_e('Export terms to POEditor', 'poeditor'); ?>">
                                <?php esc_html_e('Export', 'poeditor'); ?></a> |

                            <a href="<?php echo POEDITOR_PATH;?>&amp;do=export&amp;
                                        projectId=<?php echo esc_attr($project['id']);?>&amp;
                                        language=<?php echo esc_attr($project['code']);?>&amp;type=sync"
                               title="<?php esc_attr_e("Export terms and translations to POEditor \nOverwrites exiting translations \nDeletes obsolete terms in POEditor", 'poeditor'); ?>">
                                <?php esc_html_e('Sync', 'poeditor'); ?></a> |

                            <a href="<?php echo POEDITOR_PATH;?>&amp;do=unassignFile&amp;
                                        projectId=<?php echo esc_attr($project['id']);?>&amp;
                                        language=<?php echo esc_attr($project['code']);?>"
                               title=""><?php esc_attr_e('Unassign file', 'poeditor'); ?></a>
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
                            <?php $project_new_language = sprintf(__('Add language to %s', 'poeditor'), '"'.$project['name'].'"' ); ?>
                            <a href="#addLanguage" class="addLanguageButton button-secondary" rel="<?php echo esc_attr($project['id']);?>">+ <?php echo esc_html($project_new_language) ;?></a>
                            <form action="<?php echo POEDITOR_PATH;?>&amp;do=addLanguage" class="addLanguage" id="<?echo esc_attr('addLanguage_' . $project['id']);?>" method="post">
                                <?= wp_nonce_field('addLang_nonce'); ?>

                                <select name="language">
                                    <?php
                                    foreach ($languages as $code => $language) {
                                        ?>
                                        <option value="<?php echo esc_attr($code);?>"><?php echo esc_html($language);?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" value="<?php echo esc_attr($project['id']);?>" name="project">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Add language', 'poeditor'); ?>">
                                <a href="#" class="cancelAddLanguage" rel="<?php echo esc_attr($project['id']);?>"><?php esc_html_e('Cancel', 'poeditor'); ?></a>
                            </form>
                        </td>
                        <td ></td>
                        <td ></td>
                        <td class="poeditorToRight">
                            <?php if($project['code'] && $links) { ?>
                                <?php $import_lang = printf(__('Do you really like to download all language files for %s from POEditor.com?', 'poeditor'), $project['name']); ?>
                                <a onclick='return confirm("<?php echo esc_html($import_lang); ?>");'
                                   href="<?php echo POEDITOR_PATH;?>&amp;do=import_all&amp;
                                   projectId=<?php echo esc_attr($project['id']);?>"
                                   title="<?php esc_attr_e('Import .po and .mo files from POEditor for all languages', 'poeditor'); ?>">
                                    <?php esc_html_e('Import all', 'poeditor'); ?></a>
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
                    <a href="#addProject" class="addProjectButton button-secondary">+ <?php esc_html_e('Create project', 'poeditor'); ?></a>
                    <form action="<?php echo POEDITOR_PATH;?>&amp;do=addProject" class="addProject" method="post">
                        <?= wp_nonce_field('createProj_nonce'); ?>

                        <input type="text" name="project" id="projectNameInput">
                        <input type="submit" name="submit" id="submit" class="button button-primary"
                               value="<?php esc_attr_e('Create project', 'poeditor'); ?>">
                    </form>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <th>
                    <?php esc_html_e('Project', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Language', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Progress', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Assigned file', 'poeditor'); ?>
                </th>
                <th class="poeditorToRight">
                    <?php esc_html_e('Actions', 'poeditor'); ?>
                </th>
            </tr>
            </tfoot>
        </table>
        <?php
    } else {
        ?>
        <p><?php esc_html_e('Found no projects in your POEditor.com account.', 'poeditor'); ?></p>

        <a href="#addProject" class="addProjectButton button-primary">+ <?php esc_html_e('Create project', 'poeditor'); ?></a>

        <form action="<?php echo POEDITOR_PATH;?>&amp;do=addProject" class="addProject" method="post">
            <?= wp_nonce_field('createProj_nonce'); ?>

            <input type="text" name="project" id="projectNameInput">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Create project', 'poeditor'); ?>">
        </form>
        <?php
    }
    ?>

    <h2 class="title poeditorTableTitle">
        <?php esc_html_e('Local language files', 'poeditor'); ?>
    </h2>
    <a class="button-secondary poeditorTableExtraLink" href="<?php echo POEDITOR_PATH;?>&amp;do=scan"
       title="<?php esc_html_e( 'Search for local .po and .pot files', 'poeditor' ); ?>">
        <span class="buttons-icon-refresh"></span>
        <?php esc_html_e( 'Rescan for language files', 'poeditor' ); ?>
    </a>
    <?php
    if( is_array($locations) && !empty( $locations) ) {
        ?>
        <table class="widefat">
            <thead>
            <tr>
                <th>
                    <?php esc_html_e('Location', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('File', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Last changed', 'poeditor'); ?>
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
                    <?php esc_html_e('Location', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('File', 'poeditor'); ?>
                </th>
                <th>
                    <?php esc_html_e('Last changed', 'poeditor'); ?>
                </th>
            </tr>
            </tfoot>
        </table>
        <?php
    } else {
        ?>
        <br clear="both" />
        <a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php esc_attr_e( 'No language files found yet. Scan now', 'poeditor' ); ?>">
            <?php esc_html_e( 'No language files found yet. Scan now', 'poeditor' ); ?></a>
        <?php
    }
    ?>
    <div id="assignFile">
        <input type="hidden" name="project" id="assignFileProjectId" value="0">
        <input type="hidden" name="language" id="assignFileLanguageCode" value="">
        <h2 class="title">
            <?php esc_html_e('Assign a local file to a POEditor project language', 'poeditor'); ?> - <span id="assignFileProjectName"></span>
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
                        <?php esc_html_e('Location', 'poeditor'); ?>
                    </th>
                    <th width="45%">
                        <?php esc_html_e('File', 'poeditor'); ?>
                    </th>
                    <th>
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text" id="location_search" class="file-search" placeholder="<?php esc_html_e('Search for location', 'poeditor'); ?>"></td>
                    <td><input type="text" id="file_search" class="file-search" placeholder="<?php esc_html_e('Search for file name', 'poeditor'); ?>"></td>
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
                                <a class="button-secondary hasPath selectPath" rel="<?php echo base64_encode(WP_CONTENT_DIR.$folder.$file);?>"
                                   href="#select" title="<?php esc_attr_e( 'Select', 'poeditor' ); ?>">
                                    <?php esc_html_e( 'Select', 'poeditor' ); ?></a>
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
                                    <?php esc_html_e('Add new', 'poeditor');?>:
                                    <input type="text" placeholder="filename.po" name="newFilename" class="all-options" id="addNewSelect_<?php echo $i . '_' . $j;?>">
                                </td>
                                <td>
                                    <a class="button-secondary selectPath" folder="<?php echo WP_CONTENT_DIR.$folder;?>" rel="addNewSelect_<?php echo $i . '_' . $j;?>"
                                       href="#select" title="<?php esc_attr_e( 'Select', 'poeditor' ); ?>">
                                        <?php esc_html_e( 'Select', 'poeditor' ); ?></a>
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
                        <?php esc_html_e('Add location manually', 'poeditor' );?>:
                        <input type="text" name="newFilename" class="regular-text" id="addNewSelect_0_0">
                    </td>
                    <td>
                        <a class="button-secondary selectPath" rel="addNewSelect_0_0" folder="<?php echo WP_CONTENT_DIR;?>"
                           href="<?php echo POEDITOR_PATH;?>&amp;do=scan" title="<?php esc_attr_e( 'Select', 'poeditor' ); ?>">
                            <?php esc_html_e( 'Select', 'poeditor' ); ?></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small>
                            <?php esc_html_e('Example', 'poeditor'); ?>: <i>/themes/twentyeleven/languages/test.po</i>
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
                        <?php esc_html_e('Add location manually', 'poeditor' );?>:
                        <input type="text" name="newFilename" class="regular-text" id="addNewSelect_0_0">
                    </td>
                    <td>
                        <a class="button-secondary selectPath" rel="addNewSelect_0_0" folder="<?php echo WP_CONTENT_DIR;?>"
                           href="<?php echo POEDITOR_PATH;?>&amp;do=scan"
                           title="<?php esc_attr_e( 'Select', 'poeditor' ); ?>">
                            <?php esc_html_e( 'Select', 'poeditor' ); ?></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small>
                            <?php esc_html_e('Example', 'poeditor'); ?>: <i>/themes/twentyeleven/languages/test.po</i>
                        </small>
                    </td>
                </tr>
            </table>
            <a class="button-secondary" href="<?php echo POEDITOR_PATH;?>&amp;do=scan"
               title="<?php esc_attr_e( 'No language files found yet. Scan now', 'poeditor' ); ?>">
                <?php esc_html_e( 'No language files found yet. Scan now', 'poeditor' ); ?></a>
            <?php
        }
        ?>
        <a href="#cancel" class="button button-primary" id="cancelFileAssign"><?php esc_html_e('Cancel', 'poeditor'); ?></a>
    </div>
    <p>
        <img src="<?php echo plugins_url( '_resources/img/warning.png' , __FILE__ );?>" class="poeditorWarningIcon"
             alt="<?php esc_attr_e('This folder is not writable', 'poeditor'); ?>"> =
        <?php esc_html_e('The folder or file is not writable (so we are not be able to update the files with the information from poeditor.com)', 'poeditor'); ?>
    </p>

    <div id="resetConfirm">
        <h4>
            <?php esc_html_e('Are you sure you want to reset the plugin?', 'poeditor'); ?>
        </h4>
        <p>
            <?php $text_content = printf(__('This will delete all your local file assignments and it will detach your Wordpress installation from you account on %s', 'poeditor'),'POEditor.com'); ?>
            <?php echo esc_html($text_content) ?>
        </p>
        <a href="#cancel" class="button button-primary" id="poeditorCancelReset"><?php esc_html_e('Cancel', 'poeditor'); ?></a>
        <a href="<?php echo POEDITOR_PATH;?>&amp;do=clean" class="button button-primary" id="poeditorProceedWithReset"><?php esc_html_e('Reset', 'poeditor'); ?></a>
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
        jQuery("div#assignFile").scrollIntoView();
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
        jQuery("div#resetConfirm").scrollIntoView();
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