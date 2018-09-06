<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Google Maps API</title>
<style type="text/css">
#container {
	width: 1500px;
	margin: 0 auto;
}
#sample {
	width: 1500px;
	height: 700px;
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

<script src="https://sdk.amazonaws.com/js/aws-sdk-2.23.0.min.js"></script>
<!-- Amazon Cognito //-->
<script src="js/jsbn.js"></script>
<script src="js/jsbn2.js"></script>
<script src="js/sjcl.js"></script>
<script src="js/aws-cognito-sdk.min.js"></script>
<script src="js/amazon-cognito.min.js"></script>
<script src="js/amazon-cognito-identity.min.js"></script>
<script src=js/session.js></script>
</head>
<script>
var map;
var marker;
var markers;
var $full = <?php echo json_encode($fulls); ?>;
console.log($full);
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
        content: '<div class="sample">content</div>' 
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
      $('input[name="lng"]').val(lat_lng.lat());

//      document.getElementById('lat').textContent = lat_lng.lat();
//      document.getElementById('lng').textContent = lat_lng.lng();

      // マーカーを設置
      var marker = new google.maps.Marker({
        position: lat_lng,
        map: map
      });

      // 座標の中心をずらす
      // http://syncer.jp/google-maps-javascript-api-matome/map/method/panTo/
      map.panTo(lat_lng);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAoMX0o0ClpB7BGPn2XZaF4ilD2blTZJAA&callback=initMap"async></script>
<body onLoad="tm()">
<!--
  <ul>
    <li>lat: <span id="lat"></span></li>
    <li>lng: <span id="lng"></span></li>
  </ul>
-->
<form action = "db.php" method = "post">
lat: <input type="text" name="lat" id="lat" />
lng: <input type="text" name="lng" id="lng" />
<button type="submit">追加</button>
</form>
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
<div id="container">
	<div id="sample"></div>

	<p class="back"><a href="http://www.tam-tam.co.jp/tipsnote/javascript/post7755.html">LINK</a></p>
</div>
</body>
</html>
