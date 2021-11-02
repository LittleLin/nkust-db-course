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

// DB Connection
$dbHost = getenv("DB_HOST");
$dbPort = getenv("DB_PORT");
$dbName = getenv("DB_NAME");
$dbAccount = getenv("DB_ACCOUNT");
$dbPassword = getenv("DB_PASSWORD");

// Reply Message
if (array_key_exists(HTTPHeader::LINE_SIGNATURE, getallheaders())) {
    $signature = getallheaders()[HTTPHeader::LINE_SIGNATURE];
    $events = $bot->parseEventRequest($postBody, $signature);
    foreach ($events as $event) {
        $replyToken = $event->getReplyToken();
        $userInput = $event->getText();

        if ($userInput == "訂單查詢") {
            $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8", $dbAccount, $dbPassword);

            // 組裝 SQL Statement
            $sql = 'SELECT * FROM orders limit 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            // 執行並取得單筆資料
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = $bot->replyText($replyToken, "您的訂單將於 " . $row["shippedDate"] . " 出貨!");
        }
    }
}

?>
