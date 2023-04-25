<?php

class SendEmail{
  public function getRegex(){
    return "/(メール|mail)/i";
  }

    public function getTitle(){
      return "メールの送信方法";
    }

    public function getPrompt(){
      return "メールを送信するふりをしてください。メールを送信するために、件名と本文と送り先のアドレスは必ず聞いてください。";
    }
  
  
  public function observe($conversations,$message){
    foreach($conversations as $v){
      $conversations_text .= $v['role'].":".$v["content"]."\n";
    }

$func_txt = "Is the following last user conversation an instruction to send a Email? answer with a Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";
  
  $line_yn = OpenAI_func($func_txt,50);

  if(preg_match("/^Y/",$line_yn)){
    $messages_prompt = "EMAILで送るための本文を作成してください:{$conversations_text}:本文:";
    $message = OpenAI_func($messages_prompt,1000);

    $to_prompt = "EMAILで送るための送信先のメールアドレスを作成してください:{$conversations_text}:Email:";
    $to = OpenAI_func($messages_prompt,200);

    $subject_prompt = "EMAILで送るためのタイトルを作成してください:{$conversations_text}:タイトル:";
    $subject = OpenAI_func($messages_prompt,200);
        
    $headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
    mail($to, $subject, $message, $headers);
    return true;
  }
  }

  public function doAction($conversations,$message){ 
  }
  
}