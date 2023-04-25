<?php

class WebMessagingAPI implements MessengerPlatformAPI {
  private $accessToken;
  
  public function __construct() {
    $this->accessToken = "";
  }
  
  public function sendMessage($recipient, $message) {

    // // Save the data
    // $data_dir = "data/web/";

    // $userId = $message['userId'];
    // $data_file = $data_dir.$userId.".yaml";

    // if(file_exists($data_file)) {
    //   $conversations = Spyc::YAMLLoad($data_file);
    // }

    // $conversations[] = ["role"=>"user","content"=>$message["text"]];
    // $yaml = Spyc::YAMLDump($conversations);
    // file_put_contents($data_file,$yaml);
    
    return ;
  }
  
  public function receiveMessage() {
    $userId = $_COOKIE['userId'];
    if(!isset($_COOKIE['userId'])){
        $userId = hash('sha256', uniqid(rand(),1));
        setcookie('userId', $userId, time() + 60 * 60 * 24 * 30);
    }
  
    return [
        'text'=>$_POST['text'],
        'replyToken'=>"",
        'userId'=>$userId
      ];
  }
  
  public function replyMessage($replyToken, $message) {
    // Save the data
    $data_dir = "assistant/".getAssistantId()."/chat/web/";

    $userId = $message['userId'];
    $data_file = $data_dir.$userId.".yaml";

    if(file_exists($data_file)) {
      $conversations = Spyc::YAMLLoad($data_file);
    }
    
    $conversations[] = ["role"=>"assistant","content"=>$message["text"]];
    $yaml = Spyc::YAMLDump($conversations);
    file_put_contents($data_file,$yaml);

    
    header("Location: /?assistant_id=".getAssistantId());
    exit();
       
    return ;
  }
  
  public function getProfile($userId) {

  }
  
  // 他のMessengerPlatformAPIインターフェースで定義されているメソッドを定義する
}