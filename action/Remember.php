<?php

class Remember {
    public function getRegex() {
        return "/(LEARN|覚え|記憶|APPRENDRE|APRENDER|APRENDER|ÖĞRENMEK|УЧИТЬ|學習|学习|배우다)/i";
    }
  
    public function getTitle(){
        return "覚える・記憶する方法";
    }

    public function getPrompt(){
        return "覚える・記憶するように指示があった場合には覚えたと伝えて下さい";
    }

    public function observe($conversations, $message) {
        $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }

        $func_txt = "下記はassistantとuserの会話です。userは最後の会話で覚える・記憶するように指示していますか？ Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";

        $yn = OpenAI_func($func_txt, 50);

        if (preg_match("/^Y/", $yn)) {
            $func_txt = "下記の会話から、覚えるもしくは記憶するべき内用(text)とそのドュメントのタイトルをJSON形式で1つ出力して下さい。
-----------------
{$conversations_text}-----------------
Result=";

            $json = OpenAI_func($func_txt, 1000);

            // Decode the JSON data
            $data = json_decode($json, true);
            if($data["name"] && $data["text"]){
               $this->doAction($data);  
            }
                        
        }
        return;
    }

    public function doAction($data) {
     if($data['name']){ @file_put_contents("assistant/".getAssistantId()."/memory/".$data['name'].".txt", $data['text'],FILE_APPEND);
                      }
    }
}
