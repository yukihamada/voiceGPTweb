<?php

class PageClone {
    public function getRegex() {
        return "/(FILE|このページ|FICHIER|ARCHIVO|ARQUIVO|DOSYA|ФАЙЛ|檔案|文件|파일)/i";
    }
  
    public function getTitle(){
        return "このページの変更・追加";
    }

    public function getPrompt(){
        return "このページを変更する際には変更内容を確認して作成許可を得て作成を完了させてください。";
    }

    public function observe($conversations, $message) {
        $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }

//         $func_txt = "Is the following last user conversation an instruction to change a this page? answer with a Y or N?:
// -----------------
// {$conversations_text}-----------------
// Answer:";

        $line_yn = OpenAI_func($func_txt, 50);

//        if (preg_match("/^Y/", $line_yn)) {

$messages = array();
$messages[] = ["role"=>"system","content"=>"あなたは優秀なエンジニアです"];
$messages[] = ["role"=>"user","content"=>"current source code:".file_get_contents("../index.php")."
Generate new HTML based on current source code and work instructions:".$text]; 

$AIresponse = OpenAI_turbo($messages);
dd($AIresponse["choices"][0]["message"]["content"]);
preg_match_all("/(.*?)<html>(.*)<\/html>(.*?)/s",$AIresponse["choices"][0]["message"]["content"],$matchs);

$html = "include( \$_SERVER['DOCUMENT_ROOT'].'/init.php');
<html>".$matchs[2][0]."\n<?php
include( \$_SERVER['DOCUMENT_ROOT'].'/footer.php');
include( \$_SERVER['DOCUMENT_ROOT'].'/console.php');
?>"."\n</html>";

// Save the data
  $data_dir = "gen/";
  $md5hash = md5(date("Y_m_d_His")."CWBsalt");
  
  $data_file = $data_dir.substr($md5hash, 0, 2)."/".substr($md5hash, 2, 2)."/".$md5hash.".php";
  
  if(!file_exists($data_dir.substr($md5hash, 0, 2))){
      mkdir($data_dir.substr($md5hash, 0, 2));
  }

if(!file_exists($data_dir.substr($md5hash, 0, 2)."/".substr($md5hash, 2, 2))){
      mkdir($data_dir.substr($md5hash, 0, 2)."/".substr($md5hash, 2, 2));
  }
  
  if($prev_data_file!="index.php"){
    $prev_data_file = "/".$data_dir.$prev_data_file;
  }else{
    $prev_data_file = "/".$prev_data_file;
  }
  file_put_contents($data_file, '<?php'."\n {$response}\n".'define("PREV_FILE", "'.$prev_data_file.'"); ?>'.$html);


          $message["prompt"] = "作成されたファイル:".$data_file;
          return $message;


            // Extract the file name, directory name, and file content
            $file_name = $data['file'];
            $directory_name = $data['directory'];
            $file_content = $data['text'];
            $file_path = $directory_name . $file_name;
            $this->doAction($data);
            
//        }
        return;
    }

    public function doAction($data) {      @file_put_contents("assistant/".getAssistantId()."/data/".$data['directory'].$data['file'], $data['text']);
    }
}
