<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ぼっち飯回避なるか．．．？</title>

  <script>
    function tm(){
        tm = setInterval("location.reload()",30000);
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
<h1>待機中．．．</h1>

<?php
  require '../vendor/autoload.php';

  date_default_timezone_set('JST');

  use Aws\DynamoDb\Exception\DynamoDbException;
  use Aws\DynamoDb\Marshaler;

  $sdk = new Aws\Sdk([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest'
  ]);

  $dynamodb = $sdk->createDynamoDb();
  $marshaler = new Marshaler();
  $tableName = 'hgk-db';


  function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }
  $my_name = htmlspecialchars($_POST['username']);
  $your_name = htmlspecialchars($_GET['username']);
  if(isset($own_user) or isset($your_name)){
    //done check
    //doneがtrueなら抜ける
    $eav = $marshaler->marshalJson('
      { ":done": true
        ":who" : '+ $my_name + '
      }
    ');
    $params = [
      'TableName' => $tableName,
      'KeyConditionExpression' => '#done = :done' and '#who = :who',
      'ExpressionAttributeNames'=> [ '#done' => 'done' , '#who' => 'with_lunch'],
      'ExpressionAttributeValues'=> $eav
    ];
    try {
      $result = $dynamodb->query($params);
      echo "done";
      $url = "test.php";
      $data = array(
        'your_name' => $your_name
      );
      $content = http_build_query($data);
      $options = array('http' => array(
        'method' => 'POST',
        'content' => $content
      ));
      $contents = file_get_contents($url, false, stream_context_create($options));
    }
      // echo '<a href=search_success.php target=\"_self\"></a>';

    }
    catch (DynamoDbException $e) {
      echo "error";
      echo '<a href=index.php target=_self>マップに戻る</a>';
      
    }


    //your_nameのuser_listを取得
    $eav = $marshaler->marshalJson('
      { ":name": ' + $your_name  + '
      }
    ');

    $params = [
      'TableName' => $tableName,
      'KeyConditionExpression' => '#n = :name',
      'ExpressionAttributeNames'=> [ '#n' => 'user_name' ],
      'ExpressionAttributeValues'=> $eav
    ];
    try {
      $result = $dynamodb->query($params);
      echo "Query succeeded.\n";
      $current_list = $marshaler->unmarshalValue($result['user_list'])
      echo $current_list . "\n";
    }
    catch (DynamoDbException $e) {
      echo "Unable to query:\n";
      echo $e->getMessage() . "\n";
    }
    for($i = 0 ; $i < count($current_list); $i++){
      if ($current_list[$i] == $my_name){
        $new_list = $current_list
        $new_list[] = $my_name

        $param = [
          'TableName' => $tableName,
          'Key' => [
            'use_name' => [ 'S' => $your_name ]
          ],
          'UpdateExpression' => 'SET user_list = new_list',
          'ExpressionAttributeValues' =>[
          'new_list' => $new_list
          ]
        ];
        try {
          $response = $dynamodb->updateItem($param);
        } 
        catch(Exception $e) {
          echo "Unable to query:\n";
          echo $e->getMessage() . "\n";
        }

        break;
      }
    }
  }
  else{
    echo "No target user. Please retry.\n";
    echo '<a href=index.php target=>戻る</a>';

  }
?>






</body>
</html>
