<?php
set_time_limit(0);
class EzTGException extends Exception {
}
class EzTG {
  private $settings;
  private $offset;
  private $json_payload;
  public function __construct($settings, $base = false) {
    $this->settings = array_merge(array(
      'endpoint' => 'https://api.telegram.org',
      'token' => '1234:abcd',
      'callback' => function($update, $EzTG) {
        echo 'no callback' . PHP_EOL;
      },
      'objects' => true,
      'throw_telegram_errors' => true,
      'magic_json_payload' => true
    ), $settings);
    if ($base !== false)
      return true;
    if (!is_callable($this->settings['callback']))
      $this->error('Invalid callback.', true);
    if (php_sapi_name() === 'cli') {
      $this->settings['magic_json_payload'] = false;
      $this->offset = -1;
      $this->get_updates();
    } else {
      if ($this->settings['magic_json_payload'] === true) {
        ob_start();
        $this->json_payload = false;
        register_shutdown_function(array($this, 'send_json_payload'));
      }
      $this->processUpdate(json_decode(file_get_contents('php://input')));
    }
  }
  private function get_updates() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/getUpdates');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    while (true) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'offset=' . $this->offset . '&timeout=10');
      if ($this->settings['objects'] === true) {
        $result = json_decode(curl_exec($ch));
        if (isset($result->ok) and $result->ok === false)
          $this->error($result->description, false);
        elseif (isset($result->result)) {
          foreach ($result->result as $update) {
            if (isset($update->update_id))
              $this->offset = $update->update_id + 1;
            $this->processUpdate($update);
          }
        }
      } else {
        $result = json_decode(curl_exec($ch), true);
        if (isset($result['ok']) and $result['ok'] === false)
          $this->error($result['description'], false);
        elseif (isset($result['result'])) {
          foreach ($result['result'] as $update) {
            if (isset($update['update_id']))
              $this->offset = $update['update_id'] + 1;
            $this->processUpdate($update);
          }
        }
      }
    }
  }
  public function processUpdate($update) {
    $this->settings['callback']($update, $this);
  }
  protected function error($e, $throw = 'default') {
    if ($throw === 'default')
      $throw = $this->settings['throw_telegram_errors'];
    print_r($throw);
    if ($throw === true)
      throw new EzTGException($e);
    else {
      echo 'Telegram error: ' . $e . PHP_EOL;
      return array(
        'ok' => false,
        'description' => $e
      );
    }
  }
  public function newKeyboard($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array())) {
    return new EzTGKeyboard($type, $rkm);
  }
  public function __call($name, $arguments) {
    if (!isset($arguments[0]))
      $arguments[0] = array();
    if (!isset($arguments[1]))
      $arguments[1] = true;
    if ($this->settings['magic_json_payload'] === true and $arguments[1] === true) {
      if ($this->json_payload === false) {
        $arguments[0]['method'] = $name;
        $this->json_payload = $arguments[0];
        return 'json_payloaded'; //xd
      } elseif(is_array($this->json_payload)) {
        $old_payload = $this->json_payload;
        $arguments[0]['method'] = $name;
        $this->json_payload = $arguments[0];
        $name = $old_payload['method'];
        $arguments[0] = $old_payload;
        unset($arguments[0]['method']);
        unset($old_payload);
      }
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/' . urlencode($name));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments[0]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($this->settings['objects'] === true)
      $result = json_decode(curl_exec($ch));
    else
      $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if ($this->settings['objects'] === true) {
      if (isset($result->ok) and $result->ok === false)
        return $this->error($result->description);
      if (isset($result->result))
        return $result->result;
    } else {
      if (isset($result['ok']) and $result['ok'] === false)
        return $this->error($result['description']);
      if (isset($result['result']))
        return $result['result'];
    }
    return $this->error('Unknown error', false);
  }
  public function send_json_payload() {
    if (is_array($this->json_payload)) {
      ob_end_clean();
      echo json_encode($this->json_payload);
      header('Content-Type: application/json');
      ob_end_flush();
      return true;
    }
  }
}
class EzTGKeyboard {
  public function __construct($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array())) {
    $this->line = 0;
    $this->type = $type;
    if ($type === 'inline')
      $this->keyboard = array(
        'inline_keyboard' => array()
      );
    else
      $this->keyboard = $rkm;
    return $this;
  }
  public function add($text, $callback_data = NULL, $type = 'auto') {
    if ($this->type === 'inline') {
      if ($callback_data === NULL)
        $callback_data = trim($text);
      if (!isset($this->keyboard['inline_keyboard'][$this->line]))
        $this->keyboard['inline_keyboard'][$this->line] = array();
      if ($type === 'auto')
        if (filter_var($callback_data, FILTER_VALIDATE_URL))
          $type = 'url';
        else
          $type = 'callback_data';
      array_push($this->keyboard['inline_keyboard'][$this->line], array(
        'text' => $text,
        $type => $callback_data
      ));
    } else {
      if (!isset($this->keyboard['keyboard'][$this->line]))
        $this->keyboard['keyboard'][$this->line] = array();
      array_push($this->keyboard['keyboard'][$this->line], $text);
    }
    return $this;
  }
  public function newline() {
    $this->line++;
    return $this;
  }
  public function done() {
    if ($this->type === 'remove')
      return '{"remove_keyboard": true}';
    else
      return json_encode($this->keyboard);
  }
}
