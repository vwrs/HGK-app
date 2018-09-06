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
<script src=js/session.js></script>
	
<!-- jquery //-->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

</head>

<body>
<meta charset = "UFT-8">
<title>フォームからデータを受け取る</title>
</head>
<body>
<h1>フォームデータの送信</h1>
<form action = "db.php" method = "post">
<p>
ID：<input type = "text" name ="id" id="username"><br/>
</p>
<p>
緯度：<input type = "text" name ="lat" value="<?=$_POST['lat']?>"><br/>
</p>
<p>
経度：<input type = "text" name ="lng" value="<?=$_POST['lng']?>"><br/>
</p>
<p>
仕事：<input type = "text" name ="job"><br/>
</p>
<p>
性別：<input type = "text" name ="gender"><br/>
</p>
<input type = "submit" value ="送信">
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

$jsonstr = utf8_encode('
    {
        "user_id": ' . $id . ',
        "timestamp": 111,
        "jsonform": {
            "lat": ' . $lat . ',
            "lng": ' . $lng .',
	    "job": "' . $job .'",
	    "gender": "' . $gender .'"
        }
    }
');
# echo '<pre>';
# var_dump(utf8_encode($jsonstr));
# echo '</pre>';
# echo '<pre>';
# var_dump(json_decode(utf8_encode($jsonstr)));
# echo '</pre>';
# switch (json_last_error()) {
#         case JSON_ERROR_NONE:
#             echo ' - No errors';
#         break;
#         case JSON_ERROR_DEPTH:
#             echo ' - Maximum stack depth exceeded';
#         break;
#         case JSON_ERROR_STATE_MISMATCH:
#             echo ' - Underflow or the modes mismatch';
#         break;
#         case JSON_ERROR_CTRL_CHAR:
#             echo ' - Unexpected control character found';
#         break;
#         case JSON_ERROR_SYNTAX:
#             echo ' - Syntax error, malformed JSON';
#         break;
#         case JSON_ERROR_UTF8:
#             echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
#         break;
#         default:
#             echo ' - Unknown error';
#         break;
#     }


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
