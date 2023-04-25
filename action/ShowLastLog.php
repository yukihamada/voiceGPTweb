<?php

class ShowLastLog {
    public function getRegex() {
        return "/(最後のログ)/i";
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
      // $buf = shell_exec("tail logs/OpenAI_turbo_".date("Ymd").".log -n80");
      // $message["interrupt_response"] = $buf;
      // $message["nolog"] = true; 
      
      return $message;
    }
}
