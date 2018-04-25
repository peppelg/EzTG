<?php

namespace TRIGGEREDNICK\EzTG;

class Keyboard
{
    public function __construct($type = 'keyboard', $rkm = ['resize_keyboard' => true, 'keyboard' => []], $secure = false)
    {
        $this->line = 0;
        $this->type = $type;
        $this->secure = $secure;
        if ($type === 'inline') {
            $this->keyboard = ['inline_keyboard' => []];
        } else {
            $this->keyboard = $rkm;
        }

        return $this;
    }

    public function add($text, $callback_data = null, $type = 'auto')
    {
        if ($this->type === 'inline') {
            if ($callback_data === null) {
                $callback_data = trim($text);
            }
            if (!isset($this->keyboard['inline_keyboard'][$this->line])) {
                $this->keyboard['inline_keyboard'][$this->line] = [];
            }
            if ($type === 'auto') {
                if (filter_var($callback_data, FILTER_VALIDATE_URL)) {
                    $type = 'url';
                } else {
                    $type = 'callback_data';
                }
            }
            if ($type === 'callback_data' and $this->secure) {
                $callback_data = '!'.json_encode([
                    'c' => $callback_data,
                    'h' => hash('crc32b', $callback_data.';'.$this->secure),
                ]);
            }
            array_push($this->keyboard['inline_keyboard'][$this->line], [
                'text' => $text,
                $type  => $callback_data,
            ]);
        } else {
            if (!isset($this->keyboard['keyboard'][$this->line])) {
                $this->keyboard['keyboard'][$this->line] = [];
            }
            array_push($this->keyboard['keyboard'][$this->line], $text);
        }

        return $this;
    }

    public function newline()
    {
        $this->line++;

        return $this;
    }

    public function done()
    {
        if ($this->type === 'remove') {
            return json_encode(['remove_keyboard' => true]);
        } else {
            return json_encode($this->keyboard);
        }
    }
}
