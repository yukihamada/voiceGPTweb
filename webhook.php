<?php
include('init.php');

$apiType = $_GET["apiType"] ?? 'web';

if(!$_SESSION['refresh_token'] && $apiType=="web"){
  header("Location: /");
  exit();
}

new Webhook($apiType);


class Webhook{

  private $api;

    public function __construct($apiType) {
        $this->apiType = $apiType;
        $this->api = new MessengerAPI($apiType);
        $this->processMessage();
    }

    private function processMessage() {
        $message = $this->api->receiveMessage();
        $this->assistant_id = $this->getAssistantId($message);
        $this->data_dir = "assistant/".getAssistantId()."/chat/".$this->apiType."/";
        $this->assistantInfo = $this->getAssistantInfo($this->assistant_id);
        $conversations = $this->getHistory($message);
      
        $this->saveTrainingData($message["text"], 'user');

        $message = $this->action($conversations, $message);
        $prompt = $this->getPrompt($conversations, $message);

        if($message["userPrompt"]){
          $prompt = $message["userPrompt"]."\n";
        }else{
          $prompt = $this->assistantInfo["assistant"]["prompt"] . $message["prompt"] . $prompt;
        }

        $messages = [];
        $messages[] = ["role" => "system", "content" => $prompt];
        foreach ($conversations as $v) {
            $messages[] = [
                "role" => $v["role"],
                "content" => strlen($v["content"]) > $substr_length ? mb_substr($v["content"], 0, $substr_length) : $v["content"]
            ];
        }  

        $response = $this->getAIResponse($messages);

        if (!$response["text"]) {
          $response["text"] = $this->handleEmptyResponse($conversations, $this->assistantInfo, $prompt);
        }

        $this->saveTrainingData($response["text"], 'assistant');
        $this->sendReply($message, $response);
    }

private function sendReply($message, $response) {

    if($message["groupId"]){
//      exit();
    }
    
    // Create a reply object with the necessary information
    $message["text"] = $response["text"];

    if ($message['groupId']) {
      $this->saveConversation($message['groupId'], $message["text"],"assistant");
    }else{
      $this->saveConversation($message['userId'], $message["text"],"assistant");
    }
  
    if($this->assistantInfo["assistant"]["LINE_ICON"]){    
       $message["sender"] = [
          "name"=>$this->assistantInfo["assistant"]["name"],
          "iconUrl"=>$this->assistantInfo["assistant"]["LINE_ICON"]
         ];
    }

    try {
        $this->api->replyMessage($message['replyToken'],$message);
    } catch (Exception $e) {
        error_log('Error sending reply: ' . $e->getMessage());
    }
}

    private function getAssistantId($message)
    {
      if($message["groupId"]){
        return getAssistantId($message["groupId"]);      
      }else{
        return getAssistantId($message["userId"]);              
      }
    }
  
    private function getAssistantInfo($assistant_id)
    {
        return getAssistantInfo($assistant_id);
    }
  
    private function saveTrainingData($text, $role) {
        $dir = 'assistant/' . $this->assistant_id . '/training/untrained/';
        file_put_contents($dir . date("Ymd") . ".txt", $role . ":{$text}\n", FILE_APPEND);
    }

     private function prepareMessages($assistantInfo, $conversations, $prompt, $substr_length=1000) {
        $messages = [];
        $messages[] = ["role" => "system", "content" => $assistantInfo["assistant"]["prompt"] . $prompt];
        foreach ($conversations as $v) {
            $messages[] = [
                "role" => $v["role"],
                "content" => strlen($v["content"]) > $substr_length ? mb_substr($v["content"], 0, $substr_length) : $v["content"]
            ];
        }
        return $messages;
    }
    

    private function getAIResponse($messages) {

      $openAI = new OpenAIAPI(getenv('OpenAI_APIKEY'));

      if($this->apiType=="sse"){
        $stream = true;
      }

      if (count($messages) > 15) {
          $first_message = $messages[0];
          $last_seven_messages = array_slice($messages, -15);
          $new_messages = array_merge(array($first_message), $last_seven_messages);
      } else {
          $new_messages = $messages;
      }
            
      $text = $openAI->getResponse($new_messages, 1500, 0,$stream); 
      return ["text" => $text];
    }

    private function handleEmptyResponse($conversations, $assistantInfo, $prompt) {
        $messages = $this->prepareMessages($assistantInfo, array_slice($conversations, -7), $prompt,80);
        $result = $this->getAIResponse($messages);

        if (!$result["text"]) {
          $result["text"] = "NO TEXT ERROR";
        }

        return $result["text"];
    }
  
public function getPrompt($conversations, $message)
{
    $actionPrompts = $this->loadActionPrompts();
    $textFiles = $this->loadTextFiles();

    $conversationText = "";

    foreach ($conversations as $v) {
        $conversationText .= $v['role'] . ":" . $v["content"] . "\n";
    }

    $result = array_merge(array_keys($actionPrompts), $textFiles);

    $prompt = str_replace(
        ['{conversationText}', '{fileList}', '{inputText}'],
        [$conversationText, implode(",", $result), $message["text"]],
        $GLOBALS["DEFAULT_CHOOSE_PROMPT_TMPL"]
    );
    
    $openAI = new OpenAIAPI(getenv('OpenAI_APIKEY'));
    $text = $openAI->getPromptResponse($prompt, 200);
  
    $text = preg_replace('/[ 　\'‘’"“”「」]/u', '', $text);
    $txt = explode(",", $text);

    if($txt){ 
        foreach($txt as $k=>$v){
          $txt[$k] = trim($v);
        }
    }
      
    $result = array_merge(array_keys($actionPrompts), $txt);

    $dir = 'assistant/' . $this->assistant_id . '/text';

    if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    $prompt = "\n{$GLOBALS["DEFAULT_SEPARATOR"]}\n";
    $prompt .= "Current time is " . date("Y-m-d H:i:s T") . ".\n";
    // $prompt .= "[USER INFO]\n";
    // $prompt .= "HTTP_USER_AGENT={$_SERVER['HTTP_USER_AGENT']}\n";
    // $prompt .= "HTTP_ACCEPT_LANGUAGE={$_SERVER['HTTP_ACCEPT_LANGUAGE']}\n";
    // $prompt .= "REMOTE_ADDR={$_SERVER['REMOTE_ADDR']}\n";
    // $prompt .= "CHAT_AI_USERHASH={$_COOKIE["userId"]}\n";

    foreach ($txt as $t) {
        $t = trim($t);
        if (file_exists($dir . "/" . $t)) {
            $prompt .= "\n" . file_get_contents($dir . "/{$t}");
        }else{
            $prompt .= "\n" . $actionPrompts[$t];
        }
    }

    $prompt .= "\n\n{$GLOBALS["DEFAULT_INFOMATION"]}\n{$GLOBALS["DEFAULT_SEPARATOR"]}";

    return $prompt;
}

private function loadActionPrompts()
{
    $dir = './action';
    $files = scandir($dir);
    $actionPrompts = [];

    foreach ($files as $file) {
        if (preg_match('/^([a-zA-Z0-9]+)\.php$/', $file, $match)) {
            require_once $dir . '/' . $file;

            $class = new $match[1]();
            $actionPrompts[$class->getTitle() . ".txt"] = $class->getPrompt();
        }
    }

    return $actionPrompts;
}

private function loadTextFiles()
{
    $dir = 'assistant/' . $this->assistant_id . '/text';
    $files = scandir($dir);
    $textFiles = [];

    foreach ($files as $file) {
        if (preg_match('/^(.*?)\.txt$/', $file, $match)) {
            $textFiles[] = $file;
        }
    }
    return $textFiles;
}


public function action($conversations, $message)
{
    $actionDirs = [
        './assistant/' . getAssistantId() . '/action',
        './action',
    ];

    $regex = [];

    foreach ($actionDirs as $dir) {
        $files = scandir($dir);

        foreach ($files as $file) {
            if (preg_match('/^([a-zA-Z0-9]+)\.php$/', $file, $match)) {
                require_once $dir . '/' . $file;

                $class = new $match[1]();
                $regex[$match[1]] = $class->getRegex();
            }
        }
    }

    foreach ($regex as $obj => $r) {
        if ($r && preg_match($r, $message["text"])) {
            $class = new $obj;
            $output = $class->observe($conversations, $message);

            if (!empty($output["interrupt_response"])) {
                $output["text"] = $output["interrupt_response"];
                $this->api->replyMessage($message['replyToken'], $output);
                exit();
            }
            if($output["prompt"]){
              $message["prompt"] = $output["prompt"];
            }
            // $class->doAction($conversations, $message);
        }
    }
  return $message;
}    

public function getHistory($message) {

  
    if ($message['groupId']) {
      $this->saveConversation($message['groupId'], $message["text"],"user");
      return $this->loadConversation($message['groupId']);
    }else{
      $this->saveConversation($message['userId'], $message["text"],"user");
      return $this->loadConversation($message['userId']);
    }
    
}

private function saveConversation($identifier, $text, $role) {
    $conversations = $this->loadConversation($identifier);
    $conversations[] = ["role" => $role, "content" => $text, "datetime" => date("Y-m-d H:i:s T")];
    $yaml = Spyc::YAMLDump($conversations);

    if ($yaml) {
        file_put_contents($this->data_dir . $identifier . ".yaml", $yaml);
    }
}

private function loadConversation($identifier) {
    $conversations = [];

    if (file_exists($this->data_dir . $identifier . ".yaml")) {
        $conversations = Spyc::YAMLLoad($this->data_dir . $identifier . ".yaml");
    }
    return $conversations;
}

}


