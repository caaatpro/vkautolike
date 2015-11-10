<?php
/*
Чтобы получить токен -- перейдите по ссылке, разрешните доступ и из адресной строки скопируйте занчение access_token
http://oauth.vk.com/authorize?response_type=token&client_id=4984672&scope=offline,photos,audio,video,wall
*/
$tokens_file = 'tokens.txt'; // файл с токенами
$posts_count = 10; // сколько записей лайкнуть
$group_id = 100017009; // id группы

$tokens=@file($tokens_file);
$tokens_count = count($tokens);

if ($tokens_count <= 0) {
    echo "Файл с токенами пуст!";
    exit();
}

$response = curl('https://api.vk.com/method/wall.get?owner_id=-' . $group_id . '&count=' . $posts_count);
$json = json_decode($response, 1);

for ($i=0; $i<$tokens_count; $i++) {
    $token = rtrim($tokens[$i]);
    echo 'Лайкаем токеном: '.$token.'\n';
    foreach ($json['response'] as $key => $value) {
        if (isset($value['id'])) {
                $response = curl('https://api.vk.com/method/likes.add?type=' . $value['post_type'] . '&owner_id=' . $value['to_id'] . '&item_id=' . $value['id'] . '&access_token=' . $token);
        }
    }
}

function curl($url, $post = false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4 AlexaToolbar/alxg-3.1');
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $response = curl_exec ($ch);
    curl_close($ch);
    return $response;
}
