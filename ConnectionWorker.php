<?php
/**
 * Created by Tyler Mckenzie
 * Date: 4/11/2019
 *
 * This class is using PECL's pthreads library.
 * In order to use this class you have to be running php7.0-ztx^
 *
 */

//include "../../vendor/autoload.php";

use \DEP\Phoenix\Translator\Connector\ConnectorApertium;

class ConnectionWorker extends Worker
{
	// Static variables are thread local
	protected static $connector;

	protected static $connector_params;

	public function __construct(
		$connector_params
	) {
		if (!isset(self::$connector_params)) {
			self::$connector_params = $connector_params;
		}
	}

	public function getConnector()
	{
//		if(!self::$connector) {
			$connector_params = [
				"lang_from" => "eng",
				"lang_to"   => "spa",
				//"nodes"     => "http://10.209.69.227:2737/translate;http://10.210.130.238:2737/translate",
				"nodes"     => "http://localhost:2738/translate;http://localhost:2737/translate"
			];

			self::$connector = new ConnectorApertium();
			self::$connector->initializeConnector($connector_params);

			if (self::$connector->errno !== 0) {
				throw new Exception(self::$connector->error);
			}
//		} else {
//			self::$connector->close();
//
//			if (!self::$connector->open()) {
//				echo "Connector failed to open.\n";
//			}
//		}

		return self::$connector;
	}
}
