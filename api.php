<?php
	class POEditor_API {

		private $host = 'https://poeditor.com/api/';
		public $apiKey;

		function __construct() {

		}

		//makes a test request to see if the API key is correct
		function validateAPIKey() {
			$check = $this->_makeAPIRequest('available_languages');

			if( $check->response->status == 'success' ) return true;
			return false;
		}

		//creats a new project on POEditor.com
		function addProject($name) {
			return $this->_makeAPIRequest('create_project', array('name' => $name));
		}

		//gets a list of online projects
		function getProjects() {
			$projects_list = $this->_makeAPIRequest('list_projects');

			$projects = array();

			if( $projects_list ) {
				foreach ($projects_list->list AS $project) {
					//get each project's details
					$project_info = $this->_makeAPIRequest('list_languages', array('id' => $project->id));

					if( !empty($project_info->list) ) {
						foreach ($project_info->list as $language) {
							$project_item = array('name' => $project->name, 'id' => $project->id, 'language' => $language->name, 'code' => $language->code, 'percentage' => $language->percentage);	
							$projects[]   = $project_item;
						}
					} else {
						$project_item = array('name' => $project->name, 'id' => $project->id, 'language' => '', 'code' => '', 'percentage' => 0);
						$projects[]   = $project_item;
					}

				}

				return $projects;
			}

			return false;
		}

		//get a list of all languages
		function getLanguages() {
			$languages_list = $this->_makeAPIRequest('available_languages');

			if( $languages_list ) {
				$languages = (array) $languages_list->list;
				$languages = array_flip($languages);

				return $languages;
			}
			
			return false;
		}

		function addLanguage($project, $language) {
			return $this->_makeAPIRequest('add_language', array('id' => $project, 'language' => $language));
		}

		function upload($projectId, $path, $language, $overwrite, $updating, $sync) {
			$upload = $this->_makeAPIRequest('upload', array('id' => $projectId, 'language' => $language, 'file' => class_exists('CurlFile', false) ? new CURLFile($path, 'application/octet-stream') : "@{$path}", 'updating' => $updating, 'overwrite' => $overwrite, 'sync_terms' => $sync));

			return $upload;
		}

		function download($projectId, $language, $type) {
			$download = $this->_makeAPIRequest('export', array('id' => $projectId, 'language' => $language, 'type' => $type));

			return $download;
		}

		private function _makeAPIRequest($action, $data = array()) {

			$request_meta = array('action' => $action, 'api_token' => $this->apiKey);

			if( !empty($data) ) {
				$data = array_merge($request_meta, $data);
			} else {
				$data = $request_meta;
			}

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $this->host);
			curl_setopt($ch, CURLOPT_POST, count($data));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			//execute post
			$result = curl_exec($ch);

			//close connection
			curl_close($ch);

			$result = json_decode($result);

			return $result;
		}
	}
?>