<?php

class ListFiles {
    public function getRegex() {
        return "/(LIST|一覧|リスト|LISTER|LISTA|LISTAR|リスト|СПИСОК|清單|列表|목록)/i";
    }
  
    public function getTitle(){
        return "ファイルの一覧参照方法";
    }

    public function getPrompt(){
        return "ファイルの一覧を参照する際には、ディレクトリ名を確認してください。指定がなければルートディレクトリ(/)を参照してください。";
    }

    public function observe($conversations, $message) {
        $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }

        $func_txt = "Is the following last user conversation an instruction to list files? answer with a Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";

        $yn = OpenAI_func($func_txt, 50);

        if (preg_match("/^Y/", $yn)) {
            $func_txt = "下記の会話から、ディレクトリ名(directory)をJSONで出力して下さい。:
-----------------
{$conversations_text}-----------------
Result:";

            $yn = OpenAI_func($func_txt, 50);

            // Decode the JSON data
            $data = json_decode($yn, true);

            // Extract the directory name
            $directory_name = $data['directory'];
            $this->doAction($directory_name);
        }
        return;
    }

    public function doAction($directory_name) {
        $dir = "assistant/" . getAssistantId() . "/data/" . $directory_name;

      // Set the default directory if not specified
        if (!file_exists($dir)) {
            $dir = "assistant/" . getAssistantId() . "/data/";
        }
        // Get the list of files in the specified directory
        $files = scandir($dir);

        // // Print the list of files
        // echo "Files in directory {$directory_name}:\n";
        // foreach ($files as $file) {
           
        //     if ($file != "." && $file != "..") {
        //         echo $file . "\n";
        //     }
        // }
    }
}
