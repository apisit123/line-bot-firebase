<?php

$strAccessToken = "+eU+zQe8QJL9BraZ55TJLLTtUNQ1jDojYN63o5t3Skx2cnTqmXrr5lJNXUNBGVM8mSCtidORd7MgL6neDJf5uI5gKWhR3eUiKuqGNCdh/1ptR4Fdig9RCNHJo9tZUNJjjhH3N+MAtzE3+YVeAjlRIgdB04t89/1O/w1cDnyilFU=";

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$_uid = $arrJson['events'][0]['source']['userId'];

$response = $bot->getProfile($_uid);
$userData = $response->getJSONDecodedBody(); // return array  
$_no = 1;

$api_key="4csW3sDVAQwWESHj37IW_1XkRSAvhVwA";
$url = 'https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'';
$json = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'&q={"No":"'.$_msg.'"}');
$data = json_decode($json);
$isData=sizeof($data);

if (strpos($_msg, 'Order') !== false) {
  if (strpos($_msg, 'Order') !== false) {
    $x_tra = str_replace("Order","", $_msg);
    $pieces = explode("|", $x_tra);
    $_coffee=str_replace("[","",$pieces[0]);
    $_number=str_replace("]","",$pieces[1]);
    //Post New Data
    $newData = json_encode(
      array(
        'No' => $_no,
        'UserId' => $_uid,
        'Name' => $userData['displayName'],
        'Coffee' => $_coffee,
        'Number'=> $_number
      )
    );
    $_no = $_no+1;
    $opts = array(
      'http' => array(
          'method' => "POST",
          'header' => "Content-type: application/json",
          'content' => $newData
       )
    );
    $context = stream_context_create($opts);
    $returnValue = file_get_contents($url,false,$context);
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = 'ขอบคุณที่สอนครับ';
  }
}else{
  if($isData >0){
   foreach($data as $rec){
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $rec->answer;
   }
  }
}


$channel = curl_init();
curl_setopt($channel, CURLOPT_URL,$strUrl);
curl_setopt($channel, CURLOPT_HEADER, false);
curl_setopt($channel, CURLOPT_POST, true);
curl_setopt($channel, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($channel, CURLOPT_RETURNTRANSFER,true);
curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($channel);
curl_close ($channel);
?>