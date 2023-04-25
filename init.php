<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET["openExternalBrowser"])){
  unset($_GET["openExternalBrowser"]);
  $p = parse_url($_SERVER["REQUEST_URI"]);
  $query = isset($_GET) ? '?'.http_build_query($_GET) : '';
  header("Location: ".$p["path"].$query);
  exit();
}

date_default_timezone_set('Asia/Tokyo');
session_start();

spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require_once "class/".$class . '.php';
});

function AI($prompt, $cache = false) {
  if ($cache && file_exists("cache/AI_" . md5($prompt) . ".cache")) {
    return file_get_contents("cache/AI_" . md5($prompt) . ".cache");
  }

  $openAI = new OpenAIAPI(getenv('OpenAI_APIKEY'));
  $text = $openAI->getPromptResponse($prompt, 200);

  if ($cache) {
    file_put_contents("cache/AI_" . md5($prompt) . ".cache", $text);
  }
  return $text;
}

function OpenAI_func($prompt, $max_tokens = 10) {
  $messages = array();
  $messages[] = ["role" => "system", "content" => ""];
  $messages[] = ["role" => "user", "content" => $prompt];
  $init = OpenAI_turbo($messages, $max_tokens);

  if (isset($init["choices"][0]["message"]["content"])) {
    return $init["choices"][0]["message"]["content"];
  } else {
    return;
  }
}

function cut_and_process($input, $n, callable $process) {
  $output = $input;

  while (mb_strlen($output) > $n) {
    $cut_text = mb_substr($output, 0, $n);
    $output = $process($cut_text) . mb_substr($output, $n);
  }

  return $output;
}

function getAssistantId($key = "") {
  $GLOBALS["assistant_id"] = "chatweb";
  return $GLOBALS["assistant_id"]; // 修正点
}



function getAssistantInfo($assistant_id = "") {
  if (!$assistant_id) {
    $assistant_id = getAssistantId();
  }

  if (isset($GLOBALS['assistant'])) {
    return $GLOBALS['assistant'];
  }

  return $GLOBALS['assistant'];
}

function d($d){
  echo"<pre>";
  print_r($d);
}

function dd($d){
  file_put_contents('debug.txt', print_r($d,true),FILE_APPEND);
}

function sanitizeString($str) {
  // 余計なスペースを削除する
  $str = trim($str);

  // HTMLタグをエスケープする
  $str = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');

  // サニタイズされた文字列を返す
  return $str;
}

function handleFileInHashDirectory($folder, $filename, $content = null, $backupExistingFile = false) {
    // ファイル名をMD5ハッシュ値に変換
    $hash = md5($filename);

    // ハッシュ値をディレクトリ名として使用
    $hashDir = $folder . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2);
    $filePath = $hashDir . '/' . $filename;

    // ディレクトリが存在しない場合は作成
    if (!is_dir($hashDir)) {
        mkdir($hashDir, 0777, true);
    }

    // 既存のファイルをバックアップする場合
    if ($backupExistingFile && file_exists($filePath)) {
        $backupFilename = $filename . '.' . date('YmdHis');
        $backupFilePath = $hashDir . '/' . $backupFilename;
        rename($filePath, $backupFilePath);
    }

    // コンテンツが与えられた場合、ファイルをハッシュディレクトリに保存
    if ($content !== null) {
        file_put_contents($filePath, $content);
        return $content;
    }

    // ファイルが存在する場合、そのファイルの中身を返す
    return file_exists($filePath) ? file_get_contents($filePath) : "File does not exist in hash directory.";
}

$GLOBALS["DEFAULT_MAX_TOKENS"] = 4000;

// プロンプトのテンプレート

$GLOBALS["DEFAULT_SEPARATOR"] = "---------------";

$GLOBALS["DEFAULT_CHOOSE_PROMPT_TMPL"] = "Choose up to three relevant txt files for the user's request. If multiple, separate with commas.
Here's a conversation between the user and AI.
-----------------
{conversationText}
Txt files
--------------
txt:{fileList}
--------------
txt=";

$GLOBALS["DEFAULT_INFOMATION"] = "Here's a conversation between the user and AI.";

//ユーザーの要望に役立ちそうなtxtを、関連度が高い順に最大3つ、txt一覧から選んでください。複数のファイルがある場合は、カンマで区切って答えてください。
//下記はユーザーとAIの会話です。

// {inputText}　can be used.

// @see 
//  https://github.com/jerryjliu/llama_index/blob/main/gpt_index/prompts/default_prompts.py

$translations = Spyc::YAMLLoad('translations.yml');
$output_lang = $_GET['output'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$output_lang = substr($output_lang, 0, 2);
if(!$output_lang) $output_lang = 'ja';

$input_lang = $_GET['input'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$input_lang = substr($input_lang, 0, 2);
if(!$input_lang) $input_lang = 'ja';


function translate($key) {
    global $translations, $output_lang;
    return $translations[$output_lang][$key] ?? $key;
}

function AI_translate($text) {
$output_lang = $_GET['output'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$output_lang = substr($output_lang, 0, 2);
if(!$output_lang) $output_lang = 'ja';

  $trunslate_prompt = "Translate 'おかえりなさい。また会えてうれしいです。何かお困りのことはありますか？' to 'en'.:Welcome back. Good to see you again. Is there anything we can help you with?
  Translate 'Welcome back. Good to see you again. Is there anything we can help you with?' to 'jp'.:いらっしゃいませ、こんにちは。何をお探しですか？
  Translate 'いらっしゃいませ、こんにちは。何をお探しですか？' to 'jp'.:いらっしゃいませ、こんにちは。何をお探しですか？
  Translate '{$text}' to language of '{$output_lang}'.:
  ";
  return AI($trunslate_prompt,true);
}

$_GET["speak"] = translate("welcome_back_text");

$speak = sanitizeString($_GET["speak"]);
$prompt = sanitizeString($_GET["prompt"]);

//$lang = "en";
$trunslate_prompt = "Translate 'おかえりなさい。また会えてうれしいです。何かお困りのことはありますか？' to 'en'.:Welcome back. Good to see you again. Is there anything we can help you with?
Translate 'Welcome back. Good to see you again. Is there anything we can help you with?' to 'jp'.:いらっしゃいませ、こんにちは。何をお探しですか？
Translate 'いらっしゃいませ、こんにちは。何をお探しですか？' to 'jp'.:いらっしゃいませ、こんにちは。何をお探しですか？
Translate '{$speak}' to language of '{$output_lang}'.:
";
$speak = str_replace(array("\r", "\n","\"","'"), '',strip_tags((AI($trunslate_prompt,true))));

$assistant_info = getAssistantInfo();
$data_dir = "assistant/".getAssistantId()."/chat/sse/";

