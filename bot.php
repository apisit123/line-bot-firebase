<?php

$strAccessToken = "+eU+zQe8QJL9BraZ55TJLLTtUNQ1jDojYN63o5t3Skx2cnTqmXrr5lJNXUNBGVM8mSCtidORd7MgL6neDJf5uI5gKWhR3eUiKuqGNCdh/1ptR4Fdig9RCNHJo9tZUNJjjhH3N+MAtzE3+YVeAjlRIgdB04t89/1O/w1cDnyilFU=";


$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$_uid = $arrJson['events'][0]['source']['userId'];

$api_key="4csW3sDVAQwWESHj37IW_1XkRSAvhVwA";
$url = 'https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'';


$_axces = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey=4csW3sDVAQwWESHj37IW_1XkRSAvhVwA&q={"UserId":"'.$_uid.'","Access":"x"}');
$isData3=sizeof(json_decode($_axces));


if (strpos($_msg, 'Order') !== false) {

    if($isData3 < 3){

      $str = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'');
      $_buffer = json_decode($str, true);

      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/profile/'.$_uid.'');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

      $headers = array();
      $headers[] = "Authorization: Bearer {$strAccessToken}";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);
      $_ProFILE = json_decode($result, true);
      $_dispName = $_ProFILE['displayName'];
      $_imgProFILE = $_ProFILE['pictureUrl'];

      curl_close ($ch);



      $_no = sizeof($_buffer) + 1;
      $x_tra = str_replace("Order","", $_msg);
      $pieces = explode("|", $x_tra);
      $_coffee=str_replace("[","",$pieces[0]);
      
      $qry = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey=4csW3sDVAQwWESHj37IW_1XkRSAvhVwA&q={"UserId":"'.$_uid.'"}');

     // $data2 = json_decode($qry);
      $isData2=sizeof(json_decode($qry));

      if($isData2 >= 0){
        $acc = 'x';
      }

      $_successOrder = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/currentValue?apiKey=4csW3sDVAQwWESHj37IW_1XkRSAvhVwA&q');
      $_totalSuccessOrder=sizeof(json_decode($_successOrder));

      $newData = json_encode(
        array(
          'No' => $_no,
          'UserId' => $_uid,
          'roomId' => $_rId,
          'Coffee' => $_coffee,
          'PicProfile' => $_imgProFILE,
          'Name' => $_dispName,
          'Access' => $acc
        )
      );
      $opts = array(
        'http' => array(
            'method' => "POST",
            'header' => "Content-type: application/json",
            'content' => $newData
         )
      );

      $x = ($_no-$_totalSuccessOrder)*3;

      $context = stream_context_create($opts);
      $returnValue = file_get_contents($url,false,$context);
      $arrPostData = array();
      $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
      $arrPostData['messages'][0]['type'] = "text";
      $arrPostData['messages'][0]['text'] = "Order received";
      $arrPostData['messages'][1]['type'] = "text";
      $arrPostData['messages'][1]['text'] = 'Your order number '.$_no.'';
      $arrPostData['messages'][2]['type'] = "text";
      $arrPostData['messages'][2]['text'] = 'Please wait about '.$x.' minute';

      $_no = $_no+1;
  

    }else{
      $arrPostData = array();
      $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
      $arrPostData['messages'][0]['type'] = "text";
     $arrPostData['messages'][0]['text'] = 'ไม่ควรดื่มเกินวันละ 3 แก้ว!';
    /*  $arrPostData = array();
      $arrPostData['to'] = "Uabeba789147c026870a033491c1c6224";
      $arrPostData['messages'][0]['type'] = "template";
      $arrPostData['messages'][0]['altText'] = "this is a confirm template";
      $arrPostData['messages'][0]['template']['type'] = "confirm";
      $arrPostData['messages'][0]['template']['text'] = "Are you sure?";
      $arrPostData['messages'][0]['template']['actions'][0]['type'] = "message";
      $arrPostData['messages'][0]['template']['actions'][0]['label'] = "Yes";
      $arrPostData['messages'][0]['template']['actions'][0]['text'] = "yes";
      $arrPostData['messages'][0]['template']['actions'][1]['type'] = "message";
      $arrPostData['messages'][0]['template']['actions'][1]['label'] = "No";
      $arrPostData['messages'][0]['template']['actions'][1]['text'] = "no"; */
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