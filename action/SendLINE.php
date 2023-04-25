<?php

class SendLINE{
  public function getRegex(){
    return "/(ライン|LINE)/i";
  }
    public function getTitle(){
      return "LINEの送信方法";
    }

    public function getPrompt(){
      return "LINE を送信するふりをしてください。LINEを送信する際には、送りたい人の名前と内容を確認し送信してください";
    }
  
  public function observe($conversations,$message){
    return ;
  }

  public function doAction($conversations,$message){ 
  }

  
}