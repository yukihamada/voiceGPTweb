<?php

class UserRegister {
    public function getRegex() {
        return "/(ユーザー登録)/i";
    }
  
    public function getTitle(){
        return "ユーザー登録";
    }

    public function getPrompt(){
        return "userがユーザー登録を希望する場合には直ちにcw:{CHAT_AI_USERHASHの値}を送って他のメッセンジャーに打ち込むように促して下さい。";
    }

    public function observe($conversations, $message) {
//      $message = $this->doAction($message);
      return $message;
    }

    public function doAction($message) {
      
      return $message;
    }
}
