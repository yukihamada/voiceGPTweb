<?php

class SSEMessagingAPI implements MessengerPlatformAPI {
  private $accessToken;
  
  public function __construct() {
    $this->accessToken = "";
    @ini_set('zlib.output_compression', 0);
    @ini_set('output_buffering', 'off');
    @ini_set('output_handler', '');
    
    ob_implicit_flush(true);
    ob_end_flush();
    header("Content-Type: text/event-stream");
    header("Cache-Control: no-cache");
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
        $userId = bin2hex(random_bytes(16));
        setcookie('userId', $userId, time() + 60 * 60 * 24 * 30);
    }
 
    if($_GET["speak"]){
      $data_dir = "assistant/".getAssistantId()."/chat/sse/";
      $data_file = $data_dir.$userId.".yaml";
      $data_file_content = file_exists($data_file) ? Spyc::YAMLLoad($data_file) : [];
      $data_file_content[] = ["role" => "assistant", "content" => sanitizeString($_GET["speak"])];
      $yaml = Spyc::YAMLDump($data_file_content);
      @file_put_contents($data_file, $yaml);  
    }
    return [
        'text'=>sanitizeString($_GET['text']),
        'outputLang'=>sanitizeString($_GET['output']),
        'replyToken'=>"",
        'userPrompt'=>sanitizeString($_GET['prompt']),
        'userId'=>$userId
      ];
  }
  
  public function replyMessage($replyToken, $message) {
    // Save the data
    $data_dir = "assistant/".getAssistantId()."/chat/sse/";

    $userId = $message['userId'];
    $data_file = $data_dir.$userId.".yaml";

    if(file_exists($data_file)) {
      $conversations = Spyc::YAMLLoad($data_file);
    }
    
    $conversations[] = ["role"=>"assistant","content"=>$message["text"]];
    $yaml = Spyc::YAMLDump($conversations);
//    file_put_contents($data_file,$yaml);

    
    //header("Content-Type: text/json");
    //echo 1;
    exit();
       
    return ;
  }
  
  public function getProfile($userId) {

  }
  
  // 他のMessengerPlatformAPIインターフェースで定義されているメソッドを定義する
}