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

<title>待機中...</title>
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

if($_POST['username'] && $_POST['lat'] && $_POST['lng']){
  $username = $_POST['username'];
  $lat = $_POST['lat'];
  $lng = $_POST['lng'];
  $job = $_POST['job'];
  $gender = $_POST['gender'];

  $marshaler = new Marshaler();

  $jsonstr = utf8_encode('
  {
    "user_name": "' . $username . '",
    "jsonform": {
      "lat": ' . $lat . ',
      "lng": ' . $lng .',
      "job": "' . $job .'",
      "gender": "' . $gender .'"
    },
    "done": false
  }
  ');

  $item = $marshaler->marshalJson($jsonstr);

  $params = [
      'TableName' => 'hgk',
      'Item' => $item
  ];

  try {
    $result = $dynamodb->putItem($params);
    echo '<div class="alert alert-success">
  <strong>登録しました！!</strong></div>';

  } catch (DynamoDbException $e) {
    echo "Unable to add item:\n";
    echo $e->getMessage() . "\n";
  }

} else if (isset($_GET['check'])){
  $params = [
    'TableName' => 'hgk',
    'Key' => [
      'user_name' => [
        'S' => $_GET['check'],
      ],
    ]
  ];
  $result = $dynamodb->getItem($params);
  $user_list = $result['Item']['user_list']['L'];
  if ($user_list) {
    echo "<p>以下のユーザーがあなたとランチしたいと言っています！</p>";
    echo "<ul>";
    foreach ($user_list as $username) {
      echo '<li><a href="/match.php?user='.$username['S'].'">'.$username['S'] .'</a></li>';
    }
    echo "</ul>";
  } else {
    echo '<div class="alert alert-info">
  <strong>待機中...</strong></div>';
  }

} else {
  // redirect to index page
  echo "<script>window.location = '/';</script>";
}
?>


<form action = "db.php" method = "GET">
  <input type="hidden" name="check" value="check" />
  <button type="submit">Check</button>
</form>

<script>
$(function(){
  $('input[name="check"]').val(cognitoUser.username);
});

// JavaScript SDKを使う場合
// AWS.config.region = 'ap-northeast-1'; // リージョン
// AWS.config.credentials = new AWS.CognitoIdentityCredentials({
//     identitypoolid: 'ap-northeast-1:93cd62a1-0358-4296-80f9-7c10584fe0c0',
// });
// AWS.config.credentials = new AWS.CognitoIdentityCredentials({
//     identitypoolid: 'ap-northeast-1:0086633d-ff6f-46a5-8fb7-74f7c4446a69',
// });

// var dynamodb = new AWS.DynamoDB();
//
// function readItem() {
//   var params = {
//   Key: {
//     'user_name': {
//       S: cognitoUser.username
//     }
//   },
//   TableName: 'hgk'
//   };
//   dynamodb.getItem(params, function(err, data) {
//    if (err) console.log(err, err.stack); // an error occurred
//    else     console.log(data);           // successful response
//   });
//
// }
// $(function(){
//   readItem();
//
// });

</script>
</body>
</html>
