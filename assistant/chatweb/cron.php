<?php

include(__DIR__.'/../../init.php');

$dir = __DIR__.'/chat/';
$save_dir = __DIR__.'/memory/';

$files = scandir($dir);
$result = [];

// 朝の4時に実行
if(1||date("Ymdhi")==date("Ymd")."0400"){

  foreach ($files as $file) {
      if (preg_match('/^([a-zA-Z0-9]+)\.yaml$/', $file,$match)) {
          $buf = file_get_contents($dir."{$match[1]}.yaml");
  $func_txt = "次のassistantとuserの会話から重要な話を箇条書きでまとめて下さい。個人情報は含めないでください。
  -----------------
  
  {$buf}
  --------------
  Output:";
  
        $text = OpenAI_func($func_txt,1000)."\n";
        echo $text;
        file_put_contents($save_dir.date("Y年m月d日")."のやり取り.txt",$text,FILE_APPEND);      
        
      }
  }
  
  foreach ($files as $file) {
      if (preg_match('/^([a-zA-Z0-9]+)\.yaml$/', $file,$match)) {
          $buf = file_get_contents($dir."{$match[1]}.yaml");
  $func_txt = "次のassistantとuserの会話からassistantが学ぶ・覚えるべきことを箇条書きでまとめて下さい。個人情報は含めないでください。
  -----------------
  
  {$buf}
  --------------
  Output:";
  
        $text = OpenAI_func($func_txt,1000)."\n";
        echo $text;
        file_put_contents($save_dir.date("Y年m月d日")."に学んだこと.txt",$text,FILE_APPEND);      
        
      }
  }

}
  $obj = new LineMessagingAPI;

      $result = $obj->sendMessage("U8a311662a9af1f673392bf1cf07e00a0",[["type"=>"text","text"=>"ko"]]);

// 朝の9時に実行
if(1||date("YmdHi")==date("Ymd")."0900"){

  $buf = file_get_contents($save_dir.date("Y年m月d日")."に学んだこと.txt"); 

$func_txt = "{$buf}
--------------
上記のやり取りを参考にコミュニティへの日報として、おはようございますの挨拶も含め、昨日の振り返りと今後の予定を書いてください。ユーザとはコミュニティのメンバーのことです。";
  
  $text = OpenAI_func($func_txt,1000)."\n";
 
  $obj = new LineMessagingAPI;
//  $result = $obj->sendMessage("U8a311662a9af1f673392bf1cf07e00a0",[["type"=>"text","text"=>$text]]);
  $result = $obj->sendMessage("U8a311662a9af1f673392bf1cf07e00a0",[["type"=>"text","text"=>$text]]);
//  $result = $obj->sendMessage("U965ba1cf0c9d82eb1512e964e70f0bbd",[["type"=>"text","text"=>$text]]);

}