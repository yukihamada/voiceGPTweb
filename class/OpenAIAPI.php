<?php

class OpenAIApi {
    private $apiKey;
    private $apiUrl = "https://api.openai.com/v1/chat/completions";
    private $modelName;

    public function __construct($apiKey, $modelName = "gpt-3.5-turbo") {
        $this->apiKey = $apiKey;
        $this->modelName = $modelName;
    }

    private function createCurlHandler($data) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    private function prepareRequestData($messages, $maxTokens, $temperature, $stream) {
        return [
            "model" => $this->modelName,
            "messages" => $messages,
            "max_tokens" => $maxTokens,
            "temperature" => $temperature,
            "top_p" => 1,
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
            "stream" => $stream
        ];
    }


    private function logConversation($messages, $content) {
        $log = [];
        $log[] = "=====================\n" . date("Y/m/d H:i:s");

        $conversations_text = "";
        
        foreach ($messages as $message) {
            $conversations_text .= $message['role'] . ":" . $message['content'] . "\n";
        }
        

        $log[] = $conversations_text . $GLOBALS["DEFAULT_SEPARATOR"];
        $log[] = $content;
        file_put_contents("logs/OpenAI_turbo_" . date("Ymd") . ".log", implode("\n", $log) . "\n\n", FILE_APPEND);
    }

public function getResponse($messages, $maxTokens = 2000, $temperature = 0, $stream = false) {
    $data = $this->prepareRequestData($messages, $maxTokens, $temperature, $stream);

    // Create cURL handler
    $ch = $this->createCurlHandler($data);
    $response = '';

    // Configure write function for streaming mode
    if ($stream) {
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$response) {
            $response .= $data;
            echo $data;
            ob_flush();
            flush();
            return strlen($data);
        });
      curl_exec($ch);
    }else{
      $response = curl_exec($ch);    
    }

    curl_close($ch);

    if (!$stream) {
        $result = json_decode($response, true);
        $content = $result["choices"][0]["message"]["content"];
        $this->logConversation($messages, $content);
        return $content;
    } else {
        $lines = explode(PHP_EOL, $response);
        $parsedData = [];
        foreach ($lines as $line) {
            if (strpos($line, 'data: ') === 0) {
                $json = substr($line, 6);
                $data = json_decode($json, true);

                if ($data !== null) {
                    $parsedData[] = $data;
                }
            }
        }

        $text = "";
        foreach ($parsedData as $item) {
            $text .= $item['choices'][0]['delta']['content'];
        }

        $this->logConversation($messages, $text);
        return $text;
    }
}

    public function getPromptResponse($prompt, $maxTokens = 2000, $temperature = 0, $stream = false) {

      $messages = array();
//      $messages[] = ["role"=>"system","content"=>"貴方は答えのみを回答するAIです"];
      $messages[] = ["role"=>"user","content"=>$prompt];

      $response = $this->getResponse($messages, $maxTokens, $temperature, $stream);
      return $response;
      
    }

}