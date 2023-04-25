<?php

class TelegramMessengerAPI implements MessengerPlatformAPI {
  private $accessToken;
  
  public function __construct() {
    $this->accessToken = "6011354220:AAEkntFRkX7YLSfWrFn6lqybL4S2rLt7f2w";
    //  getenv('TELEGRAM_APIKEY');
  }
  
  public function sendMessage($recipient, $message) {
    $url = 'https://api.telegram.org/bot' . $this->accessToken . '/sendMessage';
    $data = [
      'chat_id' => $recipient,
      'text' => $message
    ];
    $options = [
      'http' => [
        'method' => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }
  
  public function receiveMessage() {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);
    $message = $json['message'];
    $text = $message['text'];
    $chatId = $message['chat']['id'];
    $userId = $message['from']['id'];
    return [
      'text' => $text,
      'chatId' => $chatId,
      'userId' => $userId
    ];
  }
  
  public function replyMessage($chatId, $message) {
    $url = 'https://api.telegram.org/bot' . $this->accessToken . '/sendMessage';
    $data = [
      'chat_id' => $chatId,
      'text' => $message['text']
    ];
    $options = [
      'http' => [
        'method' => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }
  
  public function getProfile($userId) {
    $url = 'https://api.telegram.org/bot' . $this->accessToken . '/getChatMember';
    $data = [
      'chat_id' => $userId,
      'user_id' => $userId
    ];
    $options = [
      'http' => [
        'method' => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result, true);
    $firstName = $json['result']['user']['first_name'];
    $lastName = $json['result']['user']['last_name'];
    $username = $json['result']['user']['username'];
    return [
      'firstName' => $firstName,
      'lastName' => $lastName,
      'username' => $username
    ];
  }
  
  // 他のMessengerPlatformAPIインターフェースで定義されているメソッドを定義する
}
