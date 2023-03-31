<?php
class Bot {
  private $url;
  private $data;

  public function __construct($token) {
    $this->url = "https://api.telegram.org/bot$token/";
    $this->data = $this->getData();
  }

  public function getData() {
    if (empty($this->data)) {
      $raw_data = file_get_contents('php://input');
      return json_decode($raw_data, true);
    } else {
      return $this->data;
    }
  }

  public function request($method, $params) {
    return file_get_contents($this->url . $method . '?' . http_build_query($params));
  }

  public function sendMessage($chat_id, $text, $reply = 0, $markup = []) {
    $_ = $this->request('sendMessage', [
      'chat_id' => $chat_id,
      'text' => $text,
      'reply_to_message_id' => $reply,
      'reply_markup' => $markup
    ]);
    return json_decode($_, true)['result']['message_id'];
  }

  public function editMessageText($chat_id, $message_id, $text, $markup) {
    $_ = $this->request('editMessageText', [
      'chat_id' => $chat_id,
      'message_id' => $message_id,
      'text' => $text,
      'reply_markup' => $markup
    ]);
    unset($_);
  }

  public function sendVenue($chat_id, $latitude, $longitude, $title, $address, $reply = 0) {
    $_ = $this->request('sendVenue', [
      'chat_id' => $chat_id,
      'latitude' => $latitude,
      'longitude' => $longitude,
      'title' => $title,
      'address' => $address,
      'reply_to_message_id' => $reply
    ]);
    unset($_);
  }
}