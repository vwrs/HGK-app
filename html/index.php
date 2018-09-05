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

<script>
function tm(){
        tm = setInterval("location.reload()",60000);
    }
</script>
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
 infoWindow = new google.maps.InfoWindow({
        content: '<div class="sample">AWS Japan</div>' 
  });
 marker.addListener('click', function() { 
     infoWindow.open(map, marker); 
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAoMX0o0ClpB7BGPn2XZaF4ilD2blTZJAA&callback=initMap"async></script>
<body onLoad="tm()">

<div id="container">
	<div id="sample"></div>

	<p class="back"><a href="http://www.tam-tam.co.jp/tipsnote/javascript/post7755.html">LINK</a></p>
</div>
<?php

require '../vendor/autoload.php';
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
    'debug' => false,
    'endpoint' => 'http://dynamodb.ap-northeast-1.amazonaws.com/'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

    function getFullScanResult($dynamodb, $conditions)
    {
        while (true) {
            $result = $dynamodb->scan($conditions);
            $dataFromDynamo = [];
 
            if (!empty($result['Items'])) {
                $dataFromDynamo[] = $result;
            }
 
            if (isset($result['LastEvaluatedKey'])) {
                $conditions['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
            } else {
                break;
            }
        }
        return $dataFromDynamo;
    }
$tableName = 'hgk-db';
$conditions = ['TableName' => $tableName];
$fulls = getFullScanResult($dynamodb, $conditions);
var_dump($fulls);
$tableName = 'hgk-db';

?>
</body>
</html>
