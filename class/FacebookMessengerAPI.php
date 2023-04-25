<?php

class FacebookMessengerAPI implements MessengerPlatformAPI {
  private $accessToken;
  
  public function __construct() {
    $this->accessToken = $accessToken;
  }
  
  public function sendMessage($recipient, $message) {
    $url = 'https://graph.facebook.com/v12.0/me/messages';
    $params = [
      'access_token' => $this->accessToken,
      'messaging_type' => 'RESPONSE',
      'recipient' => [
        'id' => $recipient
      ],
      'message' => $message
    ];
    $options = [
      'http' => [
        'method' => 'POST',
        'content' => http_build_query($params),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }
  
  public function receiveMessage() {
    // Facebook Messenger APIでは、受信したメッセージを受信するためのWebhookを設定する必要があります。
    // このメソッドは、Webhookで受信したメッセージを処理するために使用されます。
  }
  
  public function replyMessage($recipient, $message) {
    $url = 'https://graph.facebook.com/v12.0/me/messages';
    $params = [
      'access_token' => $this->accessToken,
      'messaging_type' => 'RESPONSE',
      'recipient' => [
        'id' => $recipient
      ],
      'message' => $message
    ];
    $options = [
      'http' => [
        'method' => 'POST',
        'content' => http_build_query($params),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }
  
  public function getProfile($userId) {
    $url = 'https://graph.facebook.com/v12.0/' . $userId;
    $params = [
      'access_token' => $this->accessToken,
      'fields' => 'name,profile_pic'
    ];
    $options = [
      'http' => [
        'method' => 'GET',
        'content' => http_build_query($params),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
  }
  
  // 他のMessengerPlatformAPIインターフェースで定義されているメソッドを実装する
}