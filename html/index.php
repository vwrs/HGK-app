<?php

require '../vendor/autoload.php';
date_default_timezone_set('JST');

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
$items = $fulls[0]['Items'];
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Google Maps API</title>
<style type="text/css">
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
#map {
  width: 100%;
  height: 100%;
}
</style>

<script>
function tm(){
        tm = setInterval("location.reload()",60000);
}
</script>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>

<!-- Amazon Cognito //-->
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.23.0.min.js"></script>
<script src="js/jsbn.js"></script>
<script src="js/jsbn2.js"></script>
<script src="js/sjcl.js"></script>
<script src="js/aws-cognito-sdk.min.js"></script>
<script src="js/amazon-cognito.min.js"></script>
<script src="js/amazon-cognito-identity.min.js"></script>
<script src="js/session.js"></script>
</head>

<body onLoad="tm()">
<form action = "db.php" method = "post">
lat: <input type="text" name="lat" id="lat" />
lng: <input type="text" name="lng" id="lng" />
<button type="submit">追加</button>
</form>

<div id="map"></div>

<p class="back"><a href="http://www.tam-tam.co.jp/tipsnote/javascript/post7755.html">LINK</a></p>

<script>
var map;
var marker;
var markers;
var $full = <?php echo json_encode($fulls); ?>;

function initMap() {
  return new google.maps.Map(document.getElementById('map'), {
     // TODO: 現在地にしたい
     center: {
           lat: 35.633454,
          lng: 139.716807
       },
      zoom: 15
   });
}

function makeMarker(map, lat, lng) {
  return new google.maps.Marker({
	map : map,
	animation: google.maps.Animation.DROP,
	position : {
           lat: lat,
          lng: lng 
	}
  });
}

function markerListener(marker) {
  infoWindow = new google.maps.InfoWindow({
        content: '<div class="map">content</div>' 
  });
  marker.addListener('click', function() { 
     infoWindow.open(map, marker); 
  });
  map.addListener('click', function(e) {
    getClickLatLng(e.latLng, map);
  });
}

function getClickLatLng(lat_lng, map) {
   // 座標を表示
   $('input[name="lat"]').val(lat_lng.lat());
   $('input[name="lng"]').val(lat_lng.lng());

   // マーカーを設置
   marker = new google.maps.Marker({
     position: lat_lng,
     map: map
   });

   // 座標の中心をずらす
   // http://syncer.jp/google-maps-javascript-api-matome/map/method/panTo/
   map.panTo(lat_lng);
}

function mapCallback () {
  map = initMap();
  marker = makeMarker(map, 35.633454, 139.716807)
  markerListener(marker);
  // DynamoDBから読み込む
  <?php if ($items): ?>
  <?php foreach ($items as $item): ?>
  console.log(<?=$item['jsonform']['M']['lat']['N']?>, <?=$item['jsonform']['M']['lng']['N']?>);
    var marker_dynamo = makeMarker(map, <?=$item['jsonform']['M']['lat']['N']?>, <?=$item['jsonform']['M']['lng']['N']?>)
    markerListener(marker_dynamo);
  <?php endforeach; ?>
  <?php endif; ?>
}

</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAoMX0o0ClpB7BGPn2XZaF4ilD2blTZJAA&callback=mapCallback"></script>
</body>
</html>
