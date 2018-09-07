<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">

<!-- Amazon Cognito //-->
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.23.0.min.js"></script>
<script src="js/jsbn.js"></script>
<script src="js/jsbn2.js"></script>
<script src="js/sjcl.js"></script>
<script src="js/aws-cognito-sdk.min.js"></script>
<script src="js/amazon-cognito.min.js"></script>
<script src="js/amazon-cognito-identity.min.js"></script>
<script src="js/session.js"></script>

<!-- jquery //-->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

<meta charset = "UFT-8">
<title>フォームからデータを受け取る</title>
</head>


<body>
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
$lat = $_POST['lat'];
$lng = $_POST['lng'];
if(isset($_POST['id'])){
$id = $_POST['id'];
$job = $_POST['job'];
$gender = $_POST['gender'];

$sdk = new Aws\Sdk([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'hgk-db';
$time = time();

$jsonstr = utf8_encode('
    {
        "user_name": "' . $id . '",
        "timestamp": ' . $time . ',
        "jsonform": {
            "lat": ' . $lat . ',
            "lng": ' . $lng .',
	    "job": "' . $job .'",
	    "gender": "' . $gender .'"
        }
    }
');

$item = $marshaler->marshalJson($jsonstr);

$params = [
    'TableName' => 'hgk-db',
    'Item' => $item
];

echo '<pre>';
var_dump($item);
echo '</pre>';

try {
    $result = $dynamodb->putItem($params);
    echo "Added item: $id - $lat - $lng\n";

} catch (DynamoDbException $e) {
    echo "Unable to add item:\n";
    echo $e->getMessage() . "\n";
}
}
?>
</body>
</html>
