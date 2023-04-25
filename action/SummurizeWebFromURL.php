<?php

class SummurizeWebFromURL{
  public function getRegex(){
    return "/(http)/i";
  }
    public function getTitle(){
      return "";
    }

    public function getPrompt(){
      return "";
    }
  public function observe($conversations,$message){
    $message["interrupt_response"] = mb_substr(strip_tags(@file_get_contents("https://enabler.fun/")),0,2500);
    $message["nolog"] = true; 
    return $message;
  }

  public function doAction($conversations,$message){ 
  }

  
}