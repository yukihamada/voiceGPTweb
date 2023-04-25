<?php

class SendToDiscord
{
    public function getRegex()
    {
        return "/(Discord|ディスコード)/i";
    }

    public function getTitle()
    {
        return "Discordへの送信方法";
    }

    public function getPrompt()
    {
        return "Discordにメッセージを送信するふりをしてください。メッセージを送信するために、本文は必ず聞いてください。";
    }

    public function observe($conversations, $message)
    {
        foreach ($conversations as $v) {
            $conversations_text .= $v['role'] . ":" . $v["content"] . "\n";
        }
        $func_txt = "Is the following last user conversation an instruction to send to Discord? Answer with a Y or N?:
-----------------
{$conversations_text}-----------------
Answer:";

        $yn = OpenAI_func($func_txt, 50);
            $this->send("Discord Log", $func_txt.$yn);

        if (preg_match("/^Y/", $yn)) {
            $messages_prompt = "Discordに送るための本文を作成してください:{$conversations_text}:本文:";
            $message = OpenAI_func($messages_prompt, 1000);

            $subject_prompt = "Discordに送るためのタイトルを作成してください:{$conversations_text}:タイトル:";
            $subject = OpenAI_func($messages_prompt, 200);

            $this->send($subject, $message);
            return true;
        }
    }

    public function send($subject, $message)
    {
        $webhookUrl = 'https://discord.com/api/webhooks/1088788834663284736/loDfbZBPi5SrxBZRFJ4h72kptT2hAjf-Mvwjz_0dlOzcrN2o62wt3Nrgfxv9C-ZHUPeO';

        $data = [
            'content' => '',
            'embeds' => [
                [
                    'title' => $subject,
                    'description' => $message,
                    'color' => hexdec('00ff00')
                ]
            ]
        ];

        $options = [
            CURLOPT_URL => $webhookUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => '',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception('Curl error: ' . curl_error($curl));
        }

        curl_close($curl);
        return $response;
    }

    public function doAction($conversations, $message)
    {
    }
}
