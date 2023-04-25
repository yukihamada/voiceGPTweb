<?php

class PromptCreator {
    public function getRegex() {
        return "/(プロンプト)/i";
    }
  
    public function getTitle(){
        return "プロンプトの作成方法";
    }

    public function getPrompt(){
        return "チャットボットに話をさせるためのプロンプト（設定）と最初に話しかける言葉を聞いて下さい。確認後、生成されたURLを送って下さい。";
    }

    public function observe($conversations, $message) {
        
      
    }

}
