<?

require "../src/App.php";

error_reporting(0);

header('Content-type: application/json');

$app = new App;

$output = array();
try {
	$file = $app->recieveFile();
	$output['url'] = $file;
} catch( Exception $e ){
	$output['error'] = $e->getMessage();
}

echo json_encode($output);