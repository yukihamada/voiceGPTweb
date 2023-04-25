<?php

class GoogleSearch {
    public function getRegex() {
        return "/(Google|検索)/i";
    }
  
    public function getTitle(){
        return "検索・わからないこと・調べたらわかりそうなこと";
    }

    public function getPrompt(){
        return "Please use the search results to answer the question user is seeking.";
    }

    public function observe($conversations, $message) {
       $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }

      $prompt = "Please output one search keyword from the following conversation.
-----------------
{$conversations_text}-----------------
Result=";
    $keyword = str_replace(array("\r", "\n","\"","'"), '',strip_tags((AI($prompt,true))));
        
  
      $message["prompt"] = "\n---\nSearch KeyWord:{$keyword}\nSearch Results\n".$this->doAction($keyword);
      return $message;
    }

    public function doAction($keyword) {

      $api_key = getenv('GOOGLE_CUSTOM_API_KEY');
      $cx = "50b292d65d9ea44a0";
      $search = new GoogleCustomSearch($api_key, $cx);
      $results = $search->search($keyword);

      foreach($results["items"] as $v){
        $buf .= $v["snippet"]."\n";
      }
          
      return $buf;
    }
}

class GoogleCustomSearch {

    private $api_key;
    private $cx;
    private $url = "https://www.googleapis.com/customsearch/v1";

    function __construct($api_key, $cx) {
        $this->api_key = $api_key;
        $this->cx = $cx;
    }

    function search($query) {
        // ユーザーエージェント名を指定
        $userAgent = 'Chatweb/1.0';

        $url = $this->url . "?q=" . urlencode($query) . "&cx=" . urlencode($this->cx) . "&key=" . urlencode($this->api_key);
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: $userAgent\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $json = json_decode($response, true);
        return $json;
    }
}

