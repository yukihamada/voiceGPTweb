<?php

interface MessengerPlatformAPI {
  public function sendMessage($recipient, $message);
  public function receiveMessage();
  public function replyMessage($replyToken, $message);
  public function getProfile($userId);
  // 他のMessenger Platform APIで定義されているメソッドを定義する
}