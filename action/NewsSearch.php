<?php

class NewsSearch {
    public function getRegex() {
        return "/(News|ニュース)/i";
    }
  
    public function getTitle(){
        return "ニュース";
    }

    public function getPrompt(){
        return "ニュースから問い合わせに答えて下さい";
    }

    public function observe($conversations, $message) {
       $conversations_text = "";
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }
      $func_txt = "下記の会話から、NEWS APIで検索するための検索キーワードを1つ出力して下さい。
-----------------
{$conversations_text}-----------------
Result=";
      
      $keyword = OpenAI_func($func_txt, 100);
  
      $message["prompt"] = "\n--------------\nニュース\n".$this->doAction($keyword);
      return $message;
    }

    public function doAction($keyword) {
$keyword = "tesla";
      $api_key = getenv('NEWS_API_KEY');
      $search = new NewsAPI($api_key);
      $results = $search->search($keyword);

    foreach($results["articles"] as $key => $value){
      if($key >= 4){
        break;
      }
      $buf .= $value["title"] . "\n" . $value["description"] . "\n";
    }          
      return $buf;
    }
}

class NewsAPI {

    private $api_key;
    private $url = "https://newsapi.org/v2/everything";

    function __construct($api_key) {
        $this->api_key = $api_key;
    }

    function search($query) {
        // ユーザーエージェント名を指定
        $userAgent = 'Chatweb/1.0';

        $url = $this->url . "?q=" . urlencode($query) . "&apiKey=" . urlencode($this->api_key);
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: $userAgent\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $json = json_decode($response, true);
      dd($json);dd($url);
        return $json;
    }
}
