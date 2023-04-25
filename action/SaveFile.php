<?php

class SaveFile {
    public function getRegex() {
        return "/(FILE|ファイル|FICHIER|ARCHIVO|ARQUIVO|DOSYA|ФАЙЛ|檔案|文件|파일)/i";
    }
  
    public function getTitle(){
        return "ファイルの保存方法";
    }

    public function getPrompt(){
        return "ファイルを保存する際には、ディレクトリ名とファイル名と中身を確認してください。保存できるファイル形式はHTMLとPHPとCSSとJavaScriptとTXTとyamlファイルに限定して保存するフリをして下さい。保存するためのスクリプト等を書かないでください。指定がなければルートディレクトリ(/)に保存してください。";
    }

    public function observe($conversations, $message) {
        $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }

        $func_txt = "Is the following last user conversation an instruction to save a file? answer with a Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";

        $line_yn = OpenAI_func($func_txt, 50);

        if (preg_match("/^Y/", $line_yn)) {
            $func_txt = "下記の会話から、ファイル名(file)とディレクトリ名(directory)と内容(text)をJSONで出力して下さい?:
-----------------
{$conversations_text}-----------------
Result=";

            $line_yn = OpenAI_func($func_txt, 50);

            // Decode the JSON data
            $data = json_decode($line_yn, true);

            // Extract the file name, directory name, and file content
            $file_name = $data['file'];
            $directory_name = $data['directory'];
            $file_content = $data['text'];
            $file_path = $directory_name . $file_name;
            $this->doAction($data);
            
        }
        return;
    }

    public function doAction($data) {      @file_put_contents("assistant/".getAssistantId()."/data/".$data['directory'].$data['file'], $data['text']);
    }
}
