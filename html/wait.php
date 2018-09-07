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
  $tableName = 'hgk';


  function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }
  $my_name = 'test2';//htmlspecialchars($_GET["pinuser"]);
  $your_name = 'test';//htmlspecialchars($_GET["username"]);

  if(isset($my_name) or isset($your_name)){
    //done check
    //doneがtrueなら抜ける
    // $eav = $marshaler->marshalJson('
    //   { "done": true,
    //     "with_lunch" : "'.$my_name.'"
    //   }
    // ');
    $param = [
      'TableName' => $tableName,
      'Key' => [
        'user_name' => [ 'S' => $your_name]
      ]
    ];
    try {
      $result = $dynamodb->getItem($param);
      $items = $result['Item'];
      if ($items['done']['BOOL'] == 1 and in_array(['S' => $my_name], $items['user_list']['L'])){
        echo "<script>window.location = './match.php?user=" . $your_name . ";'</script>";
        // $url = './success.php?user=' + $your_name;
        // header('location: '. $url, true, 301);
        exit;
        // $data = array(
        //   'your_name' => $your_name,
        //   'my_name' => $my_name
        // );
        // $content = http_build_query($data);
        // $options = array('http' => array(
        //   'method' => 'POST',
        //   'content' => $content
        // ));
        // $contents = file_get_contents($url, false, stream_context_create($options));
        //echo '<li><a href="/match.php?user='.$your_name.'">'.$my_name.'</a></li>';
      }
      else {
        echo "ひとりごはん回避ならず．．．";
        echo '<a href=index.php target=_self>マップに戻る</a>';
      }
    }catch (DynamoDbException $e) {
      echo "error";
      echo '<a href=index.php target=_self>マップに戻る</a>';
    }

    //your_nameのuser_listを取得
    // $eav = $marshaler->marshalJson('
    //   { "user_name":"'.$your_name.'"
    //   }
    // ');
    $eav = $marshaler->marshalJson('
      { "done": true,
        "with_lunch" : "'.$my_name.'"
      }
    ');
    $param = [
      'TableName' => $tableName,
      'Key' => [
        'user_name' => [ 'S' => $your_name ]
      ]
    ];
    try {
      $result = $dynamodb->getItem($param);
      $items = $result['Item'];
      if ($items['user_list']['L'] != []) $current_list = $items['user_list']['L'];
      else $current_list = [];
    }catch (DynamoDbException $e) {
      echo "Unable to query:\n";
      echo $e->getMessage() . "\n";
    }
    if (!in_array($my_name, $current_list)){
      $items['user_list']['L'][] = ['S' => $my_name];
 //      echo '<pre>';
 // //     var_dump($result["Item"]);
 //      var_dump($items['user_list']['L']);
 //      echo '</pre>';
    $param = [
      'TableName' => $tableName,
      'Key' => [
      'user_name' => [ 'S' => $your_name ]
       ],
      'UpdateExpression' => 'SET user_list = :u',
      'ExpressionAttributeValues' =>[
       ':u' => $items['user_list']
       // ':u' => ['L' => [['S'=>'test']]]
      ]
  ];
    try {
      $response = $dynamodb->updateItem($param);
    } 
    catch(Exception $e) {
      echo "Unable to query:\n";
      echo $e->getMessage() . "\n";
    }
  }
  }
  else{
    echo "No target user. Please retry.\n";
    echo '<a href=index.php target=>戻る</a>';

  }
?>



<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ぼっち飯回避なるか．．．？</title>

  <script>
    function tm(){
        tm = setInterval("location.reload()",3000);
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





<body onload="tm()">
<h1>待機中．．．</h1>







</body>
</html>
