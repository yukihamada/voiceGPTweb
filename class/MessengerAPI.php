<?php

class MessengerAPI {
  private $api;
  private $apiType;

  public function __construct($apiType) {
    $this->apiType = $apiType;
    switch ($apiType) {
      case 'facebook':
        $this->api = new FacebookMessengerAPI();
        break;
      case 'line':
        $this->api = new LineMessagingAPI();
        break;
      case 'sse':
        $this->api = new SSEMessagingAPI();
        break;
      case 'discord':
        $this->api = new DiscordMessagingAPI();
        break;
      case 'cmd':
//        $this->api = new LineMessagingAPI();
        break;
      case 'api':
//        $this->api = new LineMessagingAPI();
        break;
      default:
        $this->api = new WebMessagingAPI();
        break;
      // 他のAPIに対応する処理を記述する
    }
  }

  public function getApiType() {
    return $this->apiType;
  }
  

  public function sendMessage($recipient, $message) {
    $this->api->sendMessage($recipient, $message);
  }

  public function receiveMessage() {
    return $this->api->receiveMessage();
  }
  public function replyMessage($replyToken,$text) {
    $this->api->replyMessage($replyToken,$text);
  }
  public function getProfile() {
    $this->api->getProfile();
  }

  // 他のMessengerPlatformAPIインターフェースで定義されているメソッドを定義する
}
