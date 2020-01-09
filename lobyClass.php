<?php
	define('ZONE_US', 'US');
	define('ZONE_SING', 'Sing');
	define('ZONE_EU', 'EU');
	define('ZONE_CHINA', 'China');

	class Lobby {
		private $_mirrorUrl	= 'https://d2fr86khx60an2.cloudfront.net/';

		private $_zone = null;
		private $_lobbyFiles = array();

		// $zone permet de cibler la recherche pour éviter de lire dans tout les json
		function __construct($zone) {
			$this->_zone = $zone;
		}
		
		private function getLobbyFiles() {
			if(empty($this->_lobbyFiles)) {
				$list = file_get_contents($this->_mirrorUrl);
				$xml = simplexml_load_string($list);
				$contents = $xml->Contents;

				foreach($contents as $key => $content) {
					if (strpos($content->Key, 'Steam') === false) {
						continue;
					}

					if (!empty($this->_zone) && strpos($content->Key, $this->_zone) === false) {
						continue;
					}
					
					$this->_lobbyFiles[] = $content->Key;
				}
			}

			return $this->_lobbyFiles;
		}

		public function getServerByAddr($addr, $port) {
			$value = null;
			$lobbyfiles = $this->getLobbyFiles();

			foreach($lobbyfiles as $key => $lobby) {
				$zip = file_get_contents($this->_mirrorUrl . $lobby);
				$decode = gzdecode($zip);
				$json = json_decode($decode);

				$servers = $json->GET;

				if($servers != null) {
					foreach($servers as $index => $server) {
						if($server->__addr == $addr && $server->port == $port) {
							$value = $server;
							break;
						}
					}
				}
			}

			return $value;
		}
	}
?>
