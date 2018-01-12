<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//*************************************************************************************************************************
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;


//****************************************************************************************************************************

$strAccessToken = "+eU+zQe8QJL9BraZ55TJLLTtUNQ1jDojYN63o5t3Skx2cnTqmXrr5lJNXUNBGVM8mSCtidORd7MgL6neDJf5uI5gKWhR3eUiKuqGNCdh/1ptR4Fdig9RCNHJo9tZUNJjjhH3N+MAtzE3+YVeAjlRIgdB04t89/1O/w1cDnyilFU=";

$api_key="4csW3sDVAQwWESHj37IW_1XkRSAvhVwA";
$strUrl = "https://api.line.me/v2/bot/message/reply";

$url = 'https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'';
$json = file_get_contents('https://api.mlab.com/api/1/databases/tstdb/collections/linebot?apiKey='.$api_key.'&q={"Coffee":"'.$_msg.'""}');


$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);



$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$_uid = $arrJson['events'][0]['source']['userId'];

$data = json_decode($json);
$isData=sizeof($data);

$_no = 1;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/profile/'.$_uid.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
$headers = array();
$headers[] = "Authorization: Bearer {$strAccessToken}";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);

$_ProF = json_decode($result, true);
$_dispName = $_ProF['displayName'];
$_picProF = $_ProF['pictureUrl'];

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);



//**************************************************************************************************************************

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
        'Coffee' => $_coffee,
        'PicProfile' => $_picProF,
        'Name' => $_dispName,
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
    $arrPostData['messages'][0]['text'] = 'ขอบคุณครับ'.$result.'';
  }
}else{
  if($isData >0){
   foreach($data as $rec){
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
    $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = $rec->answer;
   }
  }else{
    $arrPostData = array();
    $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
    $arrPostData['messages'][0]['text'] = 'งง?';
    $arrPostData['messages'][1]['type'] = "text";
    $arrPostData['messages'][1]['text'] = '....'.$isData.'';
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