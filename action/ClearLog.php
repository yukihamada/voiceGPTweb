<?php

class ClearLog {
    public function getRegex() {
        return "/(ログ|クリア|Log)/i";
    }
  
    public function getTitle(){
        return "ログのクリア方法";
    }

    public function getPrompt(){
        return "If asked to clear the log, complete the clearing.";
    }

    public function observe($conversations, $message) {
 
//  file_put_contents("assistant/chatweb/chat/sse/c784e387d10377947471935039ab056b.yaml", ''.$html);
      file_put_contents("assistant/chatweb/chat/sse/{$_COOKIE["userId"]}.yaml", ''.$html);
      $message["nolog"] = true; 
      return $message;

   }


}
