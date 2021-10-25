<?php
require_once __DIR__ . '/vendor/autoload.php';

use LINE\LINEBot\Constant\HTTPHeader;

// Initialize
$channelToken = getenv("LINEBOT_CHANNEL_TOKEN");
$channelSecret = getenv("LINEBOT_CHANNEL_SECRET");
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($channelToken);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

// Get POST Body
$postBody = file_get_contents('php://input');

// Reply Message
if (array_key_exists(HTTPHeader::LINE_SIGNATURE, getallheaders())) {
    $signature = getallheaders()[HTTPHeader::LINE_SIGNATURE];
    $events = $bot->parseEventRequest($postBody, $signature);
    foreach ($events as $event) {
        $replyToken = $event->getReplyToken();
        $replyText = $event->getText();

        $response = $bot->replyText($replyToken, "您的訂單將於 10/19 14:00 出貨!");
    }
}

?>
