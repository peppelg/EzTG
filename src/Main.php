<?php

namespace peppelg\EzTG;
use \Exception;

class EzTGException extends Exception {}

class Main {
    private $settings;
    private $offset;

    public function __construct($settings){
        if(!isset($settings['endpoint'])) $settings['endpoint'] = 'https://api.telegram.org';
        if(!isset($settings['token'])) $this->error('Invalid token.');
        if(!isset($settings['secure_callbacks']))$settings['secure_callbacks'] = true;
        if(!isset($settings['callback'])) $this->error('Invalid callback.');
        if(!is_callable($settings['callback'])) $this->error('Invalid callback.');
        
        $this->settings = $settings;
        if(php_sapi_name() === 'cli'){
            $this->offset = -1;
            $this->getUpdates();
        } else {
            $this->processUpdate(json_decode(file_get_contents('php://input')));
        }
    }
    private function getUpdates() {
        $url = $this->settings['endpoint'].'/bot'.$this->settings['token'].'/getUpdates';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        while (true) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['offset' => $this->offset, 'timeout' => 10]));
            $result = json_decode(curl_exec($ch));
            if($result->ok == 0) $this->error($result->description);
            foreach($result->result as $update) {
                if(isset($update->update_id)) $this->offset = $update->update_id + 1;
                $this->processUpdate($update);
            }
        }
        curl_close($ch);
    }
    private function processUpdate($update){
        if($this->settings['secure_callbacks'] and isset($update->callback_query->data) and $update->callback_query->data[0] === '!'){
            $data = json_decode(substr($update->callback_query->data, 1), 1);
            if(is_array($data) and isset($data['h']) and isset($data['c'])){
                if ($data['h'] === hash('crc32b', $data['c'] . ';'.$this->settings['token'])) $update->callback_query->data = $data['c'];
                else $update->callback_query->data = NULL;
            } else {
                $update->callback_query->data = NULL;
            }
            $this->settings['callback']($update, $this);
        }
    }
    protected function error($e){
        throw new EzTGException($e);
    }
    public function newKeyboard($type = 'keyboard', $rkm = ['resize_keyboard' => true, 'keyboard' => []]){
        if($this->settings['secure_callbacks']) $t = $this->settings['token'];
        else $t = false;
        return new EzTGKeyboard($type, $rkm, $t);
    }
    public function __call($name, $arguments) {
        $url = $this->settings['endpoint'] . '/bot'.$this->settings['token'] . '/'.urlencode($name);

        if(!isset($arguments[0])) $arguments[0] = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments[0]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        if($result->ok == 0) $this->error($result->description);
        return $result->result;
    }
}
class EzTGKeyboard {
    public function __construct($type = 'keyboard', $rkm = ['resize_keyboard' => true, 'keyboard' => []], $secure = false){
            $this->line   = 0;
            $this->type   = $type;
            $this->secure = $secure;
            if($type === 'inline') $this->keyboard = ['inline_keyboard' => []];
            else $this->keyboard = $rkm;
            return $this;
    }
    public function add($text, $callback_data = NULL, $type = 'auto'){
        if($this->type === 'inline'){
            if($callback_data === NULL) $callback_data = trim($text);
            if(!isset($this->keyboard['inline_keyboard'][$this->line])) $this->keyboard['inline_keyboard'][$this->line] = [];
            if($type === 'auto') if(filter_var($callback_data, FILTER_VALIDATE_URL)) $type = 'url';
                                  else $type = 'callback_data';
            if($type === 'callback_data' and $this->secure){
                $callback_data = '!'.json_encode([
                    'c' => $callback_data,
                    'h' => hash('crc32b', $callback_data.';'.$this->secure)
                ]);
            }
            array_push($this->keyboard['inline_keyboard'][$this->line], [
                'text' => $text,
                $type => $callback_data
            ]);
        } else {
            if(!isset($this->keyboard['keyboard'][$this->line])) $this->keyboard['keyboard'][$this->line] = [];
            array_push($this->keyboard['keyboard'][$this->line], $text);
        }
        return $this;
    }
    public function newline(){
        $this->line++;
        return $this;
    }
    public function done() {
        if($this->type === 'remove') return json_encode(['remove_keyboard' => true]);
        else return json_encode($this->keyboard);
    }
}
