<?php
	class POEditor_API {

		private $api_url = 'https://api.poeditor.com/v2/';
		public $apiKey;

		function __construct() {

		}

		//makes a test request to see if the API key is correct
		function validateAPIKey() {
			$check = $this->_makeAPIRequest('languages/available');

			if( $check->response->status == 'success' ) {
			    return true;
            }
			return false;
		}

		//creats a new project on POEditor.com
		function addProject($name) {
			return $this->_makeAPIRequest('projects/add', array('name' => $name));
		}

		//gets a list of online projects
		function getProjects() {
			$projects_response = $this->_makeAPIRequest('projects/list');

			$projects = array();

			if($projects_response ) {
				foreach ($projects_response->result->projects AS $project) {


					//get each project's details
					$project_info = $this->_makeAPIRequest('languages/list', array('id' => $project->id));

					if(count($project_info->result->languages)) {
						foreach ($project_info->result->languages as $language) {
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
			$languages_list_response = $this->_makeAPIRequest('languages/available');

			if(count($languages_list_response->result->languages)) {
				$languages = [];
                foreach ($languages_list_response->result->languages as $lang) {
                    $languages[$lang->code] = $lang->name;
				};

				return $languages;
			}
			
			return false;
		}

		function addLanguage($project, $language) {
			return $this->_makeAPIRequest('languages/add', array('id' => $project, 'language' => $language));
		}

        function upload($projectId, $path, $language, $overwrite, $updating, $sync) {
            $upload = $this->_makeAPIRequest('projects/upload', array(
                'id' => $projectId,
                'language' => $language,
                'file' => class_exists('CurlFile', false) ? new CURLFile($path, 'application/octet-stream') : "@{$path}",
                'updating' => $updating,
                'overwrite' => $overwrite,
                'sync_terms' => $sync
            ));

            // Check if the API request was successful
            if ($upload !== null && isset($upload->response)) {
                return $upload; // API call successful, return the response
            } else {
                // Handle the case when API call failed or didn't return the expected response
                // You can log the error or take appropriate action based on your requirements.
                return (object) array('response' => (object) array('status' => 'error', 'message' => 'API call failed'));
            }
        }


		function download($projectId, $language, $type) {
			$download = $this->_makeAPIRequest('projects/export', array('id' => $projectId, 'language' => $language, 'type' => $type));

			return $download;
		}


        function getProjectLanguages($project) {
            return $this->_makeAPIRequest('languages/list', array('id' => $project));
        }

		private function _makeAPIRequest($endpoint, $data = array()) {

			$request_meta = array('api_token' => $this->apiKey);

			if( !empty($data) ) {
				$data = array_merge($request_meta, $data);
			} else {
				$data = $request_meta;
			}

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $this->api_url . $endpoint);
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