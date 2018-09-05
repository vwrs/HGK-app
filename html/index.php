<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Google Maps API</title>
<style type="text/css">
#container {
	width: 700px;
	margin: 0 auto;
}
#sample {
	width: 700px;
	height: 400px;
}
</style>
</head>
<script>
var map;
var marker;
function initMap() {
 map = new google.maps.Map(document.getElementById('sample'), {
     center: {
           lat: 35.633454, 
          lng: 139.716807
       },
      zoom: 15
   });
 marker = new google.maps.Marker({
	map : map,
	position : {
           lat: 35.633454, 
          lng: 139.716807
	}
	});
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAoMX0o0ClpB7BGPn2XZaF4ilD2blTZJAA&callback=initMap"async></script>
<body>

<div id="container">
	<div id="sample"></div>

	<p class="back"><a href="http://www.tam-tam.co.jp/tipsnote/javascript/post7755.html">LINK</a></p>
</div>
<?php

require './vendor/autoload.php';
date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

$sdk = new Aws\Sdk([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest',
    'debug' => true,
    'endpoint' => 'http://dynamodb.ap-northeast-1.amazonaws.com/'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'hgk-db';

$user_id = 100;
$timestamp = 1234;
$test = '
    {
        "user_id": ' . $user_id . ',
        "timestamp": ' . $timestamp . '
    }
';
console_log($test);

$key = $marshaler->marshalJson($test
);

$params = [
    'TableName' => $tableName,
    'Key' => $key
];

try {
    $result = $dynamodb->getItem($params);
    print_r($result["json"]);
    console_log($params);
    console_log($result);

} catch (DynamoDbException $e) {
    echo "Unable to get item:\n";
    echo $e->getMessage() . "\n";
	console_log($e);
}
?>
</body>
</html>
