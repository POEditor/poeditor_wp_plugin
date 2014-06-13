<?php
	/*
	Plugin Name: POEditor
	Plugin URI: https://poeditor.com/
	Description: This plugin will let you manage your POEditor translations directly from Wordpress via the POEditor API.
	Version: 0.9.6
	Author: POEditor
	Author URI: https://poeditor.com/
	License: GPLv2
	*/
	
	


 	class POEditor { 

 		private $api, $apiKey;

 		function __construct() {
 		

 			//define the url to the plugin's page
 			define('POEDITOR_PATH', admin_url( 'tools.php?page=poeditor'));

 			//append the POEditor element menu to the tools menu
 			add_action( 'admin_menu', array($this, 'configureMenus') );

 			//load textdomain
 			load_plugin_textdomain('poeditor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
 			
 			//get the api key
 			$this->apiKey = get_option('poeditor_apikey', false);

 			//instantiate the API class and assign the api key, if it's set
 			include_once('api.php');
			$this->api         = new POEditor_API();
			$this->api->apiKey = $this->apiKey;

 			//see if there are any error messages set and display them
 			add_action('admin_notices', array(&$this, '_displayFlashMessages'));

 			//this prevents the "Headers already sent error when trying to send flash messages before redirecting"
 			add_action('init', array(&$this, 'poeditorPreventOutput'));

 			//append all javascript actions that might be called from the code
 			add_action('admin_head', array(&$this, 'scan_javascript'));
			add_action('wp_ajax_scan', array(&$this, 'scan_callback'));
 			add_action('admin_head', array(&$this, 'get_projects_javascript'));
			add_action('wp_ajax_get_projects', array(&$this, 'get_projects_callback'));
 		}

 		/**
		 * function configureMenus
		 * 
		 * This method add the POeditor item to the Tools menu in Wordpress
		 */
 		function configureMenus() {
 			add_management_page( 'POEditor', 'POEditor', 'manage_options', 'poeditor', array(&$this, 'index'));
 		}

 		/**
		 * function index
		 * 
		 * This method creates a view for the main page of the plugin
		 */
 		function index() {

 			if( isset($_GET['do']) ) {
 				$do = $_GET['do'];
 				if( method_exists($this, $do) ) $this->$do();
 			} else {
 				//get the api key, if it is set
 				if( $this->apiKey ) {
 					
					$projects    = unserialize(get_option('poeditor_projects'));
					$locations   = unserialize(get_option('poeditor_files'));
					$languages   = unserialize(get_option('poeditor_languages'));
					$assingments = unserialize(get_option('poeditor_assingments'));

 					$this->_renderView('index', compact('locations', 'projects', 'languages', 'assingments'));
 				} else {
 					$this->_renderView('index_nokey');
 				}
 			}
 		}

 		/**
		 * function changeApiKey
		 * 
		 * This method creates a view with a form to allow the users to change the api key
		 */
 		function changeApiKey() {
 			$this->_renderView('changeApiKey');
 		}

 		/**
		 * function setApiKey
		 * 
		 * This method validates and saved the POEditor.com api key
		 */
 		function setApiKey() {
 			$this->api->apiKey = $_POST['apikey'];

 			if( $this->api->validateAPIKey() ) {
 				update_option('poeditor_apikey', $_POST['apikey']);
 				$this->_setFlashMessage(__('The API Key was succesfully changed', 'poeditor'), 'updated');

 				$projects = $this->api->getProjects();
			
				//get languages
				$languages = $this->api->getLanguages();

				update_option('poeditor_projects', serialize($projects));
				update_option('poeditor_languages', serialize($languages));

				//get the files list
				$this->_updateFiles();

 				wp_redirect(POEDITOR_PATH);
 			} else {
 				$this->_setFlashMessage(__('The API Key you set is invalid. Please try again', 'poeditor'), 'error');
 				wp_redirect(POEDITOR_PATH);
 			}
 		}

 		/**
		 * function addLanguage
		 * 
		 * This method adds a new language to an already existing project on POEditor.com
		 */
 		function addLanguage() {
 			if( !$this->api->validateAPIKey() ) {
 				update_option('poeditor_apikey', '');	
 				$this->_setFlashMessage(__('The API Key you set is invalid. Please try again', 'poeditor'), 'error');
 				wp_redirect(POEDITOR_PATH);
 			}

 			$addLanguage = $this->api->addLanguage($_POST['project'], $_POST['language']);

 			if( $addLanguage->response->status == 'success' ) {
 				$projects = $this->api->getProjects();
				update_option('poeditor_projects', serialize($projects));
				$this->_setFlashMessage(__('The language was successfully added', 'poeditor'), 'updated');
 			} else {
 				$this->_setFlashMessage($addLanguage->response->message, 'error');
 			}

 			wp_redirect(POEDITOR_PATH);

 		}

 		/**
		 * function getProjects
		 * 
		 * This method creates a view to show a preloader while the language files are being retrieved from the disk
		 */
 		function scan() {
 			$this->_renderView('scan');
 		}

 		/**
		 * function scan_javascript
		 * 
		 * The method that creates the AJAX request to retrieve all the language files from the disk
		 */
 		function scan_javascript() {
 			if( isset($_GET['do']) && $_GET['do'] == 'scan' ) {
				?>
				<script type="text/javascript" >
					jQuery(document).ready(function($) {

						var data = {
							action: 'scan'
						};

						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						jQuery.post(ajaxurl, data, function(response) {
							if( response == 'ok' ) {
								location.href = '<?php echo POEDITOR_PATH;?>';
							}
						});
					});
				</script>
				<?php
 			}
		}

		/**
		 * function scan_callback
		 * 
		 * The method that's going to be called via an AJAX request to retrieve all the language files from the disk
		 */
 		function scan_callback() {
			
			$this->_updateFiles();

			echo 'ok';

			die(); // this is required to return a proper result
 		}

 		/**
		 * function addProject
		 * 
		 * This method creates a new project on POEditor.com
		 */
 		function addProject() {
 			if( !$this->api->validateAPIKey() ) {
 				update_option('poeditor_apikey', '');
 				$this->_setFlashMessage(__('The API Key you set is invalid. Please try again', 'poeditor'), 'error');
 				wp_redirect(POEDITOR_PATH);
 			}

 			$name = $_POST['project'];

 			if( $name == '' ) {
 				$this->_setFlashMessage(__('Please set the name of the project', 'poeditor'), 'error');
 				wp_redirect(POEDITOR_PATH);
 			}

 			$response = $this->api->addProject($name);

 			if( $response->response->status == 'fail' ) {
 				$this->_setFlashMessage(sprintf(__('Project creation failed: %s', 'poeditor'), $response->response->message), 'error');
 				wp_redirect(POEDITOR_PATH);
 			} else {
 				$projects = $this->api->getProjects();
			
				update_option('poeditor_projects', serialize($projects));
				wp_redirect(POEDITOR_PATH);
 			}
 		}

 		/**
		 * function getProjects
		 * 
		 * This method creates a view to show a preloader while the projects are retrieved from POEditor.com
		 */
 		function getProjects() {
 			if( !$this->api->validateAPIKey() ) {
 				update_option('poeditor_apikey', '');
 				$this->_setFlashMessage(__('The API Key you set is invalid. Please try again', 'poeditor') , 'error');
 				wp_redirect(POEDITOR_PATH);
 			}

 			$this->_renderView('getProjects');
 		}

 		/**
		 * function get_projects_javascript
		 * 
		 * The method that creates the AJAX request to retrieve the online projects
		 */
 		function get_projects_javascript() {
 			if( isset($_GET['do']) && $_GET['do'] == 'getProjects' ) {
				?>
				<script type="text/javascript" >
					jQuery(document).ready(function($) {

						var data = {
							action: 'get_projects'
						};

						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						jQuery.post(ajaxurl, data, function(response) {
							if( response == 'ok' ) {
								location.href = '<?php echo POEDITOR_PATH;?>';
							}
						});
					});
				</script>
				<?php
 			}
		}

		/**
		 * function get_projects_callback
		 * 
		 * The method that's going to be called via an AJAX request to retrieve all the online projects
		 */
 		function get_projects_callback() {
			
			$projects = $this->api->getProjects();
			
			//get languages
			$languages = $this->api->getLanguages();

			update_option('poeditor_projects', serialize($projects));
			update_option('poeditor_languages', serialize($languages));

			echo 'ok';

			die(); // this is required to return a proper result
 		}

 		/**
		 * function assignFile
		 * 
		 * This method creates an Online project > file assignment and it saves it to the database
		 * If the file that needs to be created doesn't exist, it will be created
		 */
 		function assignFile() {

 			$path = base64_decode($_GET['path']);

 			$language = $_GET['path'];
 			//if the file doesn't exist, try to create it
 			if( !file_exists( $path ) ) {
 				$newFile = 'msgid ""
							msgstr ""
							"MIME-Version: 1.0\n"
							"Content-Type: text/plain; charset=UTF-8\n"
							"Content-Transfer-Encoding: 8bit\n"
							"X-Generator: POEditor.com\n"
							"Project-Id-Version: \n"';

 				if( file_put_contents($path, $newFile) === FALSE) {
 					if( !is_writable(dirname($path)) ) {
						$this->_setFlashMessage( sprintf(__('The %s folder is not writable. Please make it writable and try again', 'poeditor'), '<i>' . str_replace(WP_CONTENT_DIR, '', dirname($path)) . '</i>'), 'error');
						wp_redirect(POEDITOR_PATH);
					} else {
						$this->_setFlashMessage(__('The file could not be created. Please make sure that the folder is writable and your host configuration allows you to write files', 'poeditor'), 'error');
						wp_redirect(POEDITOR_PATH);
					}

					exit();
 				}
 			}

 			//update assingments
			$language  = $_GET['language'];
			$projectId = $_GET['project'];

			$key = $projectId . '_' . $language;

			$assingments       = unserialize(get_option('poeditor_assingments'));
			$assingments[$key] = $path;

			update_option('poeditor_assingments', serialize($assingments));

 			$this->_updateFiles();
 			wp_redirect(POEDITOR_PATH);
 		}

 		/**
		 * function unassignFile
		 * 
		 * This method deletes an Online project > file assignment from the database
		 */
 		function unassignFile() {
 			$language  = $_GET['language'];
			$projectId = $_GET['projectId'];

			$key = $projectId . '_' . $language;

			$assingments       = unserialize(get_option('poeditor_assingments'));

			unset($assingments[$key]);

			update_option('poeditor_assingments', serialize($assingments));

 			wp_redirect(POEDITOR_PATH);
 		}

 		/**
		 * function import_all
		 * 
		 * This method is used to bulk upload local language files with the export method
		 */
 		function export_all($projectId = '', $redirect = true) {
			
 			$projectId = !empty($projectId) ? $projectId : $_GET['projectId'];
 			$type = !empty($type) ? $type : $_GET['type'];
 			
			$projects    = unserialize(get_option('poeditor_projects'));
			$assingments = unserialize(get_option('poeditor_assingments'));
			
			$success = true;
			
			foreach($projects as $project){
				
				//if current project and a file has been assigned
				if($projectId == $project['id'] && isset($assingments[$projectId.'_'.$project['code']])){
					$success = $success && $this->export($type, $projectId, $project['code'], false);
				}
			}
			
			if($redirect) wp_redirect(POEDITOR_PATH);
			
			return $success;
 		}

 		/**
		 * function export
		 * 
		 * This method uploads a local file to POEditor.com
		 * 
		 * There can be two types of requests:
		 * - export - updates the online terms and doesn't overwrite anything
		 * - sync - updates the online definitions and overwrites everything
		 */
 		function export( $type = '', $projectId = '', $language = '', $redirect = true ) {

 			$projectId = !empty($projectId) ? $projectId : $_GET['projectId'];
 			$language = !empty($language) ? $language : $_GET['language'];
 			$type = !empty($type) ? $type : $_GET['type'];

 			switch ($type) {
 				case 'export':
 					$updating  = 'terms';
		 			$overwrite = 0;
 					break;
 				
 				case 'sync':
 					$updating  = 'definitions';
		 			$overwrite = 1;
 					break;	
 			}

			$key         = $projectId . '_' . $language;
			$assingments = unserialize(get_option('poeditor_assingments'));
			$path        = $assingments[$key];

			$languages   = unserialize(get_option('poeditor_languages'));
			
			$success = false;
			
			$upload = $this->api->upload($projectId, $path, $language, $overwrite, $updating);

			if( $upload->response->status == 'success' ) {
				if( $type == 'sync' ) {
					$this->_setFlashMessage( sprintf(__('The language file %1$s for %2$s was successfully synced', 'poeditor'), '<strong>'.(isset($languages[$language]) ? $languages[$language] : $languages[$language]).'</strong>', $projectId));
					
				} else {
					$this->_setFlashMessage( sprintf(__('The language file %1$s for %2$s was successfully uploaded to POEditor.com', 'poeditor'), '<strong>'.(isset($languages[$language]) ? $languages[$language] : $languages[$language]).'</strong>', $projectId));
				}
				$success = true;
			} else {
					$this->_setFlashMessage( sprintf(__('There was a problem with the request for %1$s: %2$s', 'poeditor'), '<strong>'.(isset($languages[$language]) ? $languages[$language] : $languages[$language]).'</strong>', '<strong>'.$upload->response->message.'</strong>', $projectId), 'error');
			}

			if($redirect) wp_redirect(POEDITOR_PATH);
			
			return $success;
 		}

 		/**
		 * function import_all
		 * 
		 * This method is used to bulk import language files with the import method
		 */
 		function import_all($projectId = '', $redirect = true) {
			
 			$projectId = !empty($projectId) ? $projectId : $_GET['projectId'];
 			
			$projects    = unserialize(get_option('poeditor_projects'));
			$assingments = unserialize(get_option('poeditor_assingments'));
			
			$success = true;
			
			foreach($projects as $project){
				
				//if current project and a file has been assigned
				if($projectId == $project['id'] && isset($assingments[$projectId.'_'.$project['code']])){
					$success = $success && $this->import($projectId, $project['code'], false);
				}
			}
			
			if($redirect) wp_redirect(POEDITOR_PATH);
			
			return $success;
 		}

 		/**
		 * function import
		 * 
		 * This method calls the API to download the language files from POEditor.com
		 * Both the .mo and the .po are downloaded
		 */
 		function import($projectId = '', $language = '', $redirect = true) {
 			
 			$projectId = !empty($projectId) ? $projectId : $_GET['projectId'];
 			$language = !empty($language) ? $language : $_GET['language'];

			$key         = $projectId . '_' . $language;
			$assingments = unserialize(get_option('poeditor_assingments'));
			$path        = $assingments[$key];

			$tmp       	= explode('.', $path);
			$type 		= end($tmp);
			
			$path = str_replace('.'.$type, '', $path);
			
			$languages   = unserialize(get_option('poeditor_languages'));
			
			$success = false;
			
			//get the extension
			$download_po = $this->api->download($projectId, $language, 'po');
			$download_mo = $this->api->download($projectId, $language, 'mo');
			
			if( $download_po->response->status == 'success' && $download_mo->response->status == 'success') {
				
				$remoteFileContents_po = file_get_contents($download_po->item);
				$remoteFileContents_mo = file_get_contents($download_mo->item);

				if( file_put_contents($path.'.po', $remoteFileContents_po) !== FALSE && file_put_contents($path.'.mo', $remoteFileContents_mo) !== FALSE ) {
					
					$this->_setFlashMessage( sprintf(__('The language file %1$s for %2$s was successfully imported from POEditor.com', 'poeditor'), '<strong>'.(isset($languages[$language]) ? $languages[$language] : $languages[$language]).'</strong>', $projectId));
					
					$success = true;
				
				} else {
					if( !is_writable(dirname($path.'.po')) ) {
					
						$this->_setFlashMessage( sprintf(__('The %s folder is not writable. Please make it writable and try again', 'poeditor'), '<i>' . str_replace(WP_CONTENT_DIR, '', dirname($path)) . '</i>'), 'error');
						
					} else if( !is_writable($path.'.po') ) {
					
						$this->_setFlashMessage( sprintf(__('The %s file cannot be overwritten. Please make it writable and try again', 'poeditor'), '<i>' . str_replace(WP_CONTENT_DIR, '', $path) . '.po</i>'), 'error');
					} else if( !is_writable($path.'.mo') ) {

						$this->_setFlashMessage( sprintf(__('The %s file cannot be overwritten. Please make it writable and try again', 'poeditor'), '<i>' . str_replace(WP_CONTENT_DIR, '', $path) . '.mo</i>'), 'error');

					} else {
					
						$this->_setFlashMessage( __('There was a problem importing the language file from POEditor.com', 'poeditor'), 'error');
					}
				}
				
			} else {
				
				$this->_setFlashMessage( $response_po->response->message);
				$this->_setFlashMessage( $response_mo->response->message);
				
			}

			if($redirect) wp_redirect(POEDITOR_PATH);
			
			return $success;
 		}

 		/**
		 * function _renderView
		 * 
		 * This method includes the desired view file in the rendering and creates the variables needed for the output
		 */
 		private function _renderView($view = null, $data = null) {
 			if( $view ) {
 				if( $data && is_array($data)) {
 					extract($data);
 				}

 				//make sure the '.php' part wasn't included in the call
 				$view = str_replace('.php', '', $view);

 				include_once('views/'. $view . '.php');
 			}
 		}

 		/**
		 * function _setFlashMessage
		 * 
		 * This method allows the plugin to display session flash messages
		 */
 		private function _setFlashMessage($message, $class = 'updated') {
            $flash_messages = maybe_unserialize(get_option('poeditor_flash_messages', ''));
            
            if(!isset($flash_messages) || !is_array($flash_messages)) $flash_messages = array();
            if(!isset($flash_messages[$class]) || !is_array($flash_messages[$class])) $flash_messages[$class] = array();
            
            $flash_messages[$class][] = $message;
            
            update_option('poeditor_flash_messages', serialize($flash_messages));
 		}

 		/**
		 * function _displayFlashMessages
		 * 
		 * This method outputs all flash messages that have been created via _setFlashMessage
		 */
 		function _displayFlashMessages() {
 			$flash_messages = unserialize(get_option('poeditor_flash_messages', serialize('')));

            if(is_array($flash_messages)) {
                foreach($flash_messages as $class => $messages) {
                    foreach($messages as $message) {
                        ?>
                        <div class="<?php echo $class; ?>"><p><?php echo $message; ?></p></div>
                        <?php
                    }
                }
            }
            
            //empty out flash messages
            update_option('poeditor_flash_messages', serialize(''));
 		}

 		/**
		 * function _updateFiles
		 * 
		 * This method scans all folders under /wp-content/ for .po and .pot files
		 */
 		private function _updateFiles() {
 			$iterator = new RecursiveDirectoryIterator(WP_CONTENT_DIR);
			$display  = array('po', 'pot');
			
			$files = array();

			foreach(new RecursiveIteratorIterator($iterator) AS $file) {
				$tmp       = explode('.', $file);
				$extension = end($tmp);

			    if (in_array(strtolower($extension), $display)) {
			    	$folder = dirname($file);
			    	$folder = str_replace(WP_CONTENT_DIR, '', $folder) . DIRECTORY_SEPARATOR;

			    	$files[$folder][] = str_replace(WP_CONTENT_DIR . $folder, '', $file);
			    }
			}

			//create a reference of the found files in the database
			update_option('poeditor_files', serialize($files));
 		}

 		/**
		 * function clean
		 * 
		 * This method deletes all data that has been written by the plugin from the database
		 */
 		function clean() {
 			delete_option('poeditor_apikey');
 			delete_option('poeditor_assingments');
 			delete_option('poeditor_projects');
 			delete_option('poeditor_languages');
 			delete_option('poeditor_files');

 			$this->_setFlashMessage(__('The plugin has been reset successfully', 'poeditor'), 'updated');
 			wp_redirect(POEDITOR_PATH);
 		}

 		//method for hooks
 		/**
		 * function poeditorRegisterPlugin
		 * 
		 * This function is called when the plugin is activated. It is the first part 
		 * of the register plugin action (the plugin needs to redirect to the API Key page)
		 * 
		 */
 		function poeditorRegisterPlugin() {
 			add_option('Activated_Plugin', 'poeditor');
 		}

 		/**
		 * function poeditorRegisterPluginRedirect
		 * 
		 * This method creates a hook on the main admin page to see if the plugin has just
		 * been activated. Is that it the case, it redirects to the API Key submit page
		 */
 		function poeditorRegisterPluginRedirect() {
 			if(is_admin() && get_option('Activated_Plugin') == 'poeditor' ) {
			    delete_option('Activated_Plugin');
			    wp_redirect(admin_url( 'tools.php?page=poeditor'));
		    }
 		}

 		/**
		 * function poeditorPreventOutput
		 * 
		 * prevents the "Headers already sent error when trying to send flash messages before redirecting"
		 */
 		function poeditorPreventOutput(){
 			ob_start();
 		}
 	}

 	$poeditor = new POEditor;

 	//add the actions that get triggered when the plugin is activated
 	register_activation_hook( __FILE__, array($poeditor, 'poeditorRegisterPlugin'));
	add_action('admin_init', array($poeditor, 'poeditorRegisterPluginRedirect'));
	
?>