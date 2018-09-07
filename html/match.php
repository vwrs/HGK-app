<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">

<!-- AWS SDK for JavaScript -->
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.283.1.min.js"></script>

<!-- jquery //-->
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>

<!-- Amazon Cognito //-->
<script src="js/jsbn.js"></script>
<script src="js/jsbn2.js"></script>
<script src="js/sjcl.js"></script>
<script src="js/aws-cognito-sdk.min.js"></script>
<script src="js/amazon-cognito.min.js"></script>
<script src="js/amazon-cognito-identity.min.js"></script>
<script src="js/session.js"></script>

<!-- bootstrap -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<title>おめでとうございます！</title>
</head>

<body>
<?php
require '../vendor/autoload.php';
date_default_timezone_set('UTC');

// save to DynamoDB
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$sdk = new Aws\Sdk([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

// $eav = $marshaler->marshalJson(utf8_encode('
//     {
//         ":d": true
//     }
// '));
// $key = $marshaler->marshalJson(utf8_encode('
//     {
//         "user_name": ' . 'hideaki' . '
//     }
// '));
// $params = [
//     'TableName' => 'hgk',
//     'Key' => $key,
//     'UpdateExpression' =>
//         'set done = :d',
//     'ExpressionAttributeValues'=> $eav,
//     'ReturnValues' => 'UPDATED_NEW'
// ];

// try {
//     $result = $dynamodb->updateItem($params);
// } catch (DynamoDbException $e) {
//   echo "Unable to add item:\n";
//   echo $e->getMessage() . "\n";
// }
echo '<div class="alert alert-success">
<strong>'.$_GET['user'].'</strong>とマッチしました！</div>';
?>

</body>
</html>
