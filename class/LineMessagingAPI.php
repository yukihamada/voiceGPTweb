<?php

class LineMessagingAPI implements MessengerPlatformAPI {
  private $accessToken;

  public function __construct() {
    $this->accessToken = getenv('LINE_ACCESS_TOKEN');
  }

  public function sendMessage($recipient, $message) {
    $url = 'https://api.line.me/v2/bot/message/push';
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->accessToken
    ];
    $data = [
      'to' => $recipient,
      'messages' => $message
    ];
    return $this->sendHttpRequest($url, $headers, $data);
  }

  public function receiveMessage() {
    $events = json_decode(file_get_contents('php://input'), true)['events'];
    foreach ($events as $event) {
      if ($event['type'] == 'message') {
        return $this->processMessageEvent($event);
      }
    }
  }

  private function processMessageEvent($event) {
    $messageType = $event['message']['type'];
    $replyToken = $event['replyToken'];
    $text = '';

    switch ($messageType) {
      case 'text':
        $text = $event['message']['text'];
        break;
    }

    return [
        'text' => $text,
        'replyToken' => $replyToken,
        'userId' => $event['source']['userId'],
        'groupId' => $event['source']['groupId'],
        'messenger_type' => 'line'
    ];
  }

  public function replyMessage($replyToken, $message) {
    if (!$message["text"]) {
      return false;
    }
    $url = 'https://api.line.me/v2/bot/message/reply';
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->accessToken
    ];
    $data = [
        "replyToken" => $replyToken,
        "messages" => [
            [
                "type" => "text",
                "text" => $message["text"],
                "sender" => $message["sender"],
            ]
        ]
    ];
    return $this->sendHttpRequest($url, $headers, $data);
  }

  private function sendHttpRequest($url, $headers, $data) {
    $options = [
      'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => json_encode($data),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }

  public function getProfile($userId) {
    $url = 'https://api.line.me/v2/bot/profile/' . $userId;
    $headers = [
      'Authorization: Bearer ' . $this->accessToken
    ];
    return $this->sendHttpRequest($url, $headers, []);
  }
}
