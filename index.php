<?php
require 'bot.php';
require 'env.php';

$bot = new Bot(Env::TOKEN);
$data = $bot->getData();

if (isset($data['message'])) {
  $message = $data['message'];
  $id = $message['message_id'];
  checkStatus($message);

  $group = $message['chat']['id'] == Env::GROUP_ID;
  $user = $message['from']['id'] == Env::USER_ID;
  $caption = $message['caption'] == Env::CAPTION;

  if ($group && $user && $caption) {
    $message_id = $bot->sendMessage(Env::GROUP_ID, Env::REPLY, $id);
    $bot->editMessageText(Env::GROUP_ID, $message_id, Env::REPLY, json_encode([
        'inline_keyboard' => [
          [
            [
              'text' => getWeather(),
              'url' => 'tg://user?id=' . Env::ADMIN_ID
            ]
          ]
        ]
    ], JSON_HEX_TAG));
    /*
    $bot->sendVenue(
        Env::ADMIN_ID,
        Env::LATITUDE,
        Env::LONGITUDE,
        Env::TITLE,
        Env::ADDRESS,
        $id
    );
    */
  }
}

function checkStatus($message) {
  global $bot;
  $id = $message['message_id'];
  $admin = $message['from']['id'] == Env::ADMIN_ID;
  $command = $message['text'] == Env::COMMAND;

  if ($admin && $command) {
    $bot->sendMessage(Env::ADMIN_ID, getWeather(), $id);
  }
}

function getWeather() {
  $data = json_decode(file_get_contents(Env::URL), true)['current'];

  $temperature = (int) $data['temp'] . Env::CELSIUS;
  $feels_like = (int) $data['feels_like'] . Env::CELSIUS;
  $description = $data['weather'][0]['description'];

  $description = mb_strtoupper(
      mb_substr($description, 0, 1)
  ) . mb_substr($description, 1);

  return "$description: $temperature ($feels_like)";
}