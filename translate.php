<?php
/**
 * Created by Tyler Mckenzie
 * Date: 4/10/2019
 */

include "../../vendor/autoload.php"; // You may need to update this depending on where you install the tool
include "./src/AsyncTranslate.php";
include "./src/ConnectionWorker.php";
include "./src/Cli.php";

new \DEP\Phoenix\Translator\Connector\ConnectorApertium();

$cli_config = [
	"-f|--file"    => "file",
	"-t|--threads" => "threads"
];

$cli = new Cli($cli_config);
$arguments = $cli->process($argv);

// Dummy text to translate # Uncomment to use
//$strings_to_translate = [
//					// Military
//					"Army",
//					"Navy",
//					"Marines",
//					"Air Force",
//					"Coast Guard",
//					"Military Veteran",
//					"Active Service",
//					"National Guard",
//					"Active Reserve",
//					"Ready Reserve",
//					"GoldStar",
//					"Honorable Discharge",
//					"Individual Ready Reserve",
//					"Military",
//					"Reserve",
//
//					// College
//					"College",
//					"Education",
//					"Student",
//					"Vocational School",
//					"Diploma",
//					"Graduate Degree",
//					"University",
//					"Collegiate",
//					"Seminary",
//					"Academics",
//					"Degree",
//					"Accredited",
//					"Two Year Degree",
//					"Graduates",
//					"Registered Nurse",
//					"Medical School",
//				];


echo "Initializing Pool\n";
$connector_params = [
	"lang_from" => "eng",
	"lang_to"   => "spa",
	"nodes"     => "http://localhost:2738/translate;http://localhost:2737/translate"
];

$pool = new Pool(4, 'ConnectionWorker', [ $connector_params ]);

$stores = [];
foreach ($strings_to_translate as $string_to_translate) {
	$store = new Volatile();

	$pool->submit(new AsyncTranslate($string_to_translate, $store));

	$stores[] = $store;
}

// Collect all the data
$pool->collect();
$pool->shutdown();

$store_file = "store_" . date("mdy_His") . ".json";
$stores_json = json_encode($stores);
echo "Saving Translations...\n";
file_put_contents($store_file, $stores_json);
