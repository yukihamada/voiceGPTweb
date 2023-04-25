<?php
include('init.php');
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Fastest voiceGPTweb</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.4.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.4.0/highlight.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3YF25NMXG8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-3YF25NMXG8');
</script>
<body>

  <div id="settings-container">
      <label for="language-select">🎤</label>
      <select id="language-select">
  <option value="en">English (US)</option>
  <option value="ja">日本語</option>
  <option value="es">Español</option>
  <option value="fr">Français</option>
  <option value="de">Deutsch</option>
  <option value="it">Italiano</option>
  <option value="pt">Português (Brasil)</option>
  <option value="ru">Русский</option>
  <option value="zh">简体中文</option>
  <option value="ko">한국어</option>
  <option value="ar">العربية</option>
  <option value="el">Ελληνικά</option>
  <option value="he">עברית</option>
  <option value="hi">हिन्दी</option>
  <option value="id">Bahasa Indonesia</option>
  <option value="nl">Nederlands</option>
  <option value="no">Norsk</option>
  <option value="sv">Svenska</option>
  <option value="th">ไทย</option>
  <option value="tr">Türkçe</option>
  <option value="vi">Tiếng Việt</option>
      </select>        

  <label for="language-select">🤖</label>
      <select id="language-select-assistant">
  <option value="en-US">English (US)</option>
<option value="ja-JP">日本語</option>
<option value="es-ES">Español</option>
<option value="fr-FR">Français</option>
<option value="de-DE">Deutsch</option>
<option value="it-IT">Italiano</option>
<option value="pt-BR">Português (Brasil)</option>
<option value="ru-RU">Русский</option>
<option value="zh-CN">简体中文</option>
<option value="ko-KR">한국어</option>
<option value="ar-SA">العربية</option>
<option value="el-GR">Ελληνικά</option>
<option value="he-IL">עברית</option>
<option value="hi-IN">हिन्दी</option>
<option value="id-ID">Bahasa Indonesia</option>
<option value="nl-NL">Nederlands</option>
<option value="no-NO">Norsk</option>
<option value="sv-SE">Svenska</option>
<option value="th-TH">ไทย</option>
<option value="tr-TR">Türkçe</option>
<option value="vi-VN">Tiếng Việt</option>

      </select>    
    <p>
    👥💬<input type="checkbox" id="continuous-checkbox">
    </p>

  </div>
  
  <div id="chat">
  <h1>voiceGPTweb</h1>
  
      <p><button id="start-button">Start</button></p>
  <div id="contents">
  <div id="instruction">
    <?php echo translate("instruction");?>
  </div>
  <div>

<div class="user-image" onclick="toggleSettings()">
  <img src="<?php echo $_SESSION["picture"];?>" />
</div>

    <p>
    <button id="startBtn">🎤</button>
    <button id="stopBtn" disabled>📤</button>
    <button id="keyboradBtn">⌨️</button>

    </p>
    
  </div>

    <textarea id="output"></textarea>
    <div id="messages">
      <div id="message" style="background-color: #444;"><?php echo $speak?></div>      
    </div>

  
<script>

const keyboradBtn = document.getElementById("keyboradBtn");
const outputTextarea = document.getElementById("output");

keyboradBtn.addEventListener("click", () => {
  instruction.style.display = "none";
outputTextarea.style.display = "block";
  outputTextarea.focus();
});

function toggleSettings() {
  const settingsContainer = document.getElementById('settings-container');
  if (settingsContainer.style.display === 'flex') {
    settingsContainer.style.display = 'none';
  } else {
    settingsContainer.style.display = 'flex';
  }
  
}

  
const chat = document.getElementById("chat");
const messages = document.getElementById("messages");
const message = document.getElementById("message");
const chatForm = document.getElementById("chat-form");
const instruction = document.getElementById("instruction");
const contents = document.getElementById("contents");
const langSelect = document.getElementById('language-select');
const langSelectAssistant = document.getElementById('language-select-assistant');
langSelectAssistant.value = <?php if($output_lang){echo "'{$output_lang}'";}else{echo "navigator.language";}?>;

window.addEventListener('load', function() {
  langSelect.value = <?php if($input_lang){echo "'{$input_lang}'";}else{echo "navigator.language";}?>;

  const userLang = <?php if($input_lang){echo "'{$input_lang}'";}else{echo "navigator.language";}?>;

  const matchingOption = Array.from(langSelectAssistant.options).find(option => {
    return option.value.startsWith(userLang.split('-')[0]);
  });

  if (matchingOption) {
    langSelectAssistant.value = matchingOption.value;
  }
  
});


langSelect.addEventListener('change', () => {
  recognition.stop();
//  alert(langSelect.value);
  recognition.lang = langSelect.value;
  recognition.start();
});
langSelectAssistant.addEventListener('change', () => {

});


let isVoiceOn = false;
  
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const continuousCheckbox = document.getElementById("continuous-checkbox");
const output = document.getElementById('output');

let recognition;


    output.addEventListener('input', function() {
      if (output.value) {
           stopBtn.disabled = false;
      }
    });

if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();

    recognition.lang = <?php if($input_lang){echo "'{$input_lang}'";}else{echo "navigator.language || 'ja'";}?>;
    recognition.interimResults = true;
    recognition.continuous = true;

    recognition.onstart = () => {
      if (isVoiceOn) {
        window.speechSynthesis.cancel();
      }
  
      startBtn.disabled = true;
    };

    recognition.onresult = (event) => {
        instruction.style.display = "none";
      outputTextarea.style.display = "block";
        let transcript = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            transcript += event.results[i][0].transcript;
          if (event.results[i].isFinal && continuousCheckbox.checked) {
            sendMessage(transcript);
            return;
          }
                
        }
        output.value = transcript;
      stopBtn.disabled = false;

    };
   
    recognition.onend = () => {
      startBtn.disabled = false;
      stopBtn.disabled = true;
    };
} else {
    startBtn.disabled = true;
    stopBtn.disabled = true;
    output.value = 'Web Speech APIに対応していません。';
}

startBtn.addEventListener('click', () => {
  recognition.start();
});

stopBtn.addEventListener('click', () => {
  recognition.stop();
  sendMessage(output.value);
});

output.addEventListener("input", function() {
  // テキストエリアの高さをリセット
  this.style.height = "auto";
  // テキストエリアの高さをスクロール高さに設定
  this.style.height = this.scrollHeight + "px";
});

continuousCheckbox.addEventListener('change', (event) => {
  if(continuousCheckbox.checked){
    recognition.start();
  }else{
    recognition.stop();  
  }

});
  
document.addEventListener("DOMContentLoaded", function() {
  const userAgent = navigator.userAgent;

  const externalURL = 'https://chatweb.ai/';
  if (userAgent.match(/twitter/)) {
    window.location.href = `twitter://open?url=${encodeURIComponent(externalURL)}`;
  } else if (userAgent.match(/(Line|FB_IAB|Instagram|Twitter)/i)) {
    const params = new URLSearchParams(window.location.search);
    params.set('openExternalBrowser', '1');

    document.getElementById('start-button').addEventListener('click', function() {
      window.open(`${window.location.pathname}?${params.toString()}`, '_blank');
      return;
    });
    document.getElementById('start-button').textContent = 'OPEN';
    
    window.location.href = `${window.location.pathname}?${params.toString()}`;
  }
});


let result = "";

function breakText(text) {
  const breakingChars = ['。', '、', '!', '?', '！', '？', '.', ','];

  for (const char of text) {
    if (breakingChars.includes(char)) {
      if (isVoiceOn) {
        speak(result,langSelectAssistant.value);
      }
      result = "";
    } else {
      result += char;
    }
  }
}

let speakCount = 0;
let speakText = '';

function speak(text, lang) {
  voiceWebAPI(text,lang);
  return;
}

function voiceWebAPI(text,lang) {
  
  const utterance = new SpeechSynthesisUtterance(text);
  const voices = window.speechSynthesis.getVoices();
  const selectedVoice = voices.find(voice => voice.lang === lang);
  
  if (selectedVoice) {
      utterance.voice = selectedVoice;
  }
  if (lang === "en-US") {
      utterance.voice = voices.find(voice => voice.name === "Samantha");
  }
  
  
  // Adjust pitch and rate
  const randomPitch = 1 + Math.random() * 0.1; // Random pitch between 1 and 1.1
  const randomRate = 1 + Math.random() * 0.1 + 0.1; 
  utterance.pitch = randomPitch;
  utterance.rate = randomRate;
  
  // utterance.lang = lang || "ja-JP";
  window.speechSynthesis.speak(utterance);
  
}
  
function formatCodeMessage(msg) {
  const codeBlockRegex = /```([^`]+)```/g;
  let formattedMsg = msg;
  let match;

  while ((match = codeBlockRegex.exec(msg)) !== null) {
    const codeBlock = match[1];
    const codeBlockWithoutBR = codeBlock.replace(/<br>/g, '\n');
    const highlightedCodeBlock = hljs.highlightAuto(codeBlockWithoutBR).value;
    formattedMsg = formattedMsg.replace(match[0], `<pre><code class="hljs line-numbers">${highlightedCodeBlock}</code></pre>`);
  }

  // 改行を<br>に置き換える
  formattedMsg = formattedMsg.replace(/\n/g, '<br>');

  return formattedMsg;
}

function sendMessage(msg) {
  if(continuousCheckbox.checked){
    msg = msg.replace(/^(ん|1|うん)/, '');
  }
  if(!msg) return;
  
  if (msg.match(/^(静かにして|うるさい|黙って|静かにしてください|黙れ|喋らないで|be quiet|shut up|silence|stop talking|安静|闭嘴|别说话|保持安静|请安静|保持沉默|cállate|silencio|deja de hablar|por favor, cállate)$/i)) return;

  const udiv = document.createElement("div");
udiv.innerText = msg;
  udiv.classList.add("user");
  messages.insertBefore(udiv,messages.firstChild);

  output.value = "";
  output.style.height = "120px;";
  output.style.display = "none";
  
  const div = document.createElement("div");
  messages.insertBefore(div,messages.firstChild);

<?php 


$params = [
  'apiType' => 'sse',
  'prompt' => $prompt,
  'speak' => $speak,
  'output' => $output_lang
];

$urlwqueryString = '`webhook.php?text=${msg}&'.http_build_query($params).'`';
?>

output.value = "";
output.style.display = "none;";
  
let eventSource = new EventSource(<?php echo $urlwqueryString?>);



  eventSource.onmessage = (e) => {
    const data = e.data;

    try {
      const parsedData = JSON.parse(data);
      response = parsedData.choices[0];
div.style.backgroundColor = "#444";
        if (!response.delta.content) {
        }else{
          div.innerHTML += response.delta.content;
          div.innerHTML = formatCodeMessage(div.innerHTML);
          breakText(response.delta.content);          
        } 
      } catch (error) {
    }


    if(data.match(/^\[DONE\]$/)){
      eventSource.close();
      return;
    } 
  }
  return;
}


const startButton = document.getElementById("start-button");
startButton.addEventListener("click", activateChat);

  function activateChat() {
    contents.style.display = "block";    
    isVoiceOn = true;
    startButton.style.display = "none";
    speak("<?php echo $speak;?>",langSelectAssistant.value);
  }

</script>
</body>
</html>