<?php

class SendSMS{
  public function getRegex(){
    return "/(SMS)/i";
  }

    public function getTitle(){
      return "SMSの送信方法";
    }

    public function getPrompt(){
      return "SMSを送信します。SMSを送信するために国際番号を含む電話番号を聞き、確認を行いSMSを送信します。";
    }
  
  
  public function observe($conversations,$message){

// お客様の電話番号と名前
$phoneNumber = "+819074090407";
$customerName = "Hamada";

// SMS送信を実行
//sendSms($phoneNumber, $customerName);

    
    foreach($conversations as $v){
      $conversations_text .= $v['role'].":".$v["content"]."\n";
    }

$func_txt = "Is the following last user conversation an instruction to send a SMS? answer with a Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";
  
    $line_yn = OpenAI_func($func_txt,50);

    if(preg_match("/^Y/",$line_yn)){
      $messages_prompt = "SMSで送るための本文を作成してください:{$conversations_text}:本文:";
      $message = OpenAI_func($messages_prompt,1000);
  
      $to_prompt = "SMSで送るための電話番号（+を含む国際番号）を作成してください:{$conversations_text}:Tel:";
      $to = OpenAI_func($messages_prompt,200);
      $this->sendSms($to, $message);
      return true;
    }
    $message["prompt"] = $to."にメッセージ送信完了しました".$message;
    return $message;
    
  }

  public function doAction($conversations,$message){ 
  }
  
}

function sendSms($phoneNumber, $customerName) {
    $accountSid = 'AC919d46dc5a4881266765a16602814157'; // TwilioのAccount SID
    $authToken = '262747f6fe8897b469e4256c4095d386'; // TwilioのAuth Token
    $twilioPhoneNumber = '5673863507'; // Twilioの電話番号

    $message = $customerName . "https://chatweb.ai/";

    $apiUrl = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";
    $postData = array(
        'From' => $twilioPhoneNumber,
        'To' => $phoneNumber,
        'Body' => $message
    );
  dd($postData);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $accountSid . ':' . $authToken);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // HTTPステータスコードが201の場合、送信成功とみなす
    if ($httpCode == 201) {
        echo "SMS送信に成功しました。\n";
    } else {
        echo "SMS送信に失敗しました。\n";
    }
}




