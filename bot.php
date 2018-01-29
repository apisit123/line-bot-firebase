<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
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


$strAccessToken = "+eU+zQe8QJL9BraZ55TJLLTtUNQ1jDojYN63o5t3Skx2cnTqmXrr5lJNXUNBGVM8mSCtidORd7MgL6neDJf5uI5gKWhR3eUiKuqGNCdh/1ptR4Fdig9RCNHJo9tZUNJjjhH3N+MAtzE3+YVeAjlRIgdB04t89/1O/w1cDnyilFU=";


$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);

$strUrl = "https://api.line.me/v2/bot/message/reply";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$_msg = $arrJson['events'][0]['message']['text'];
$_uid = $arrJson['events'][0]['source']['userId'];
$_rId = $arrJson['events'];

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

  /*    $arrPostData['messages'][3] = {
            "type":"text",
            "text":"Hello, user"
        };
*/


      $_no = $_no+1;

    }else{
      $arrPostData = array();
      $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
      $arrPostData['messages'][0]['type'] = "text";
      $arrPostData['messages'][0]['text'] = 'ไม่ควรดื่มเกินวันละ 3 แก้ว!';
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