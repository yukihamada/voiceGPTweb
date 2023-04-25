<?php

class TextEditor {
    public function getRegex() {
        return "/(ファイルの編集|edit file)/i";
    }
  
    public function getTitle(){
        return "";
    }

    public function getPrompt(){
        return "";
    }

    public function observe($conversations, $message) {
      $message = $this->doAction($message);
      return $message;
    }

    public function doAction($message) {

      $message["interrupt_response"] = "こちらから編集して下さい
https://chatweb.ai/editor.php?assistant_id=".getAssistantId()."&cwhash=".hash('sha512', date("YmdH").getAssistantId()."25033fb1a97bca37c5e859fd864f570f0d9950cf")."

URLは".date("Y年m月d日H時",time()+3600)."まで有効です";
      
      return $message;
    }
}
