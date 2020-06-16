<?php
require_once 'EzTG.php';
$callback = function ($update, $bot) {
    # here's your bot
    if (isset($update['message'])) {
        # messages "handler"
        $message_id = $update['message']['message_id']; # https://core.telegram.org/bots/api#message
        $user_id = $update['message']['from']['id'];
        $chat_id = $update['message']['chat']['id'];
        $text = $update['message']['text'];
        if ($text === '/start') {
            $bot->sendMessage(['chat_id' => $chat_id, 'text' => sprintf("EzTGPHP example bot\n\nCommands:\n/inline\n/keyboard\n/hidekb\n\nYour id: %s\nChat id: %s\nMessage id: %s", $user_id, $chat_id, $message_id)]); # you can find method parameters in https://core.telegram.org/bots/api#sendmessage
        }
        if ($text === '/inline') {
            $keyboard = $bot->newKeyboard('inline')
                ->add('example', 'callback data')
                ->add('example 2', 'callback data 2')
                ->newline()
                ->add('example 3', 'https://www.google.com')
                ->done();
            $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'Test', 'reply_markup' => $keyboard]);
        }
        if ($text === '/keyboard') {
            $keyboard = $bot->newKeyboard('keyboard')
                ->add('example')
                ->add('example 2')
                ->newline()
                ->add('example 3')
                ->done();
            $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'wewe', 'reply_markup' => $keyboard]);
        }
        if ($text === '/hidekb') {
            $keyboard = $bot->newKeyboard('remove')->done();
            $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'Adios keyboard', 'reply_markup' => $keyboard]);
        }
    }

    if (isset($update['callback_query'])) {
        # callback query "handler"
        $message_id = $update['callback_query']['message']['message_id'];
        $user_id = $update['callback_query']['from']['id'];
        $chat_id = $update['callback_query']['message']['chat']['id'];
        $cb_id = $update['callback_query']['id'];
        $cb_data = $update['callback_query']['data'];
        if ($cb_data === 'callback data') {
            $bot->answerCallbackQuery(['callback_query_id' => $cb_id, 'text' => 'example #1']); # you can find method parameters in https://core.telegram.org/bots/api#answercallbackquery
            $keyboard = $bot->newKeyboard('inline')
                ->add('example 2', 'callback data 2')
                ->done();
            $bot->editMessageText(['chat_id' => $chat_id, 'message_id' => $message_id, 'text' => 'New message', 'reply_markup' => $keyboard]); # you can find method parameters in https://core.telegram.org/bots/api#editmessagetext
        }
        if ($cb_data === 'callback data 2') {
            $bot->answerCallbackQuery(['callback_query_id' => $cb_id, 'text' => 'example #2 [alert]', 'show_alert' => true]);
            $bot->editMessageText(['chat_id' => $chat_id, 'message_id' => $message_id, 'text' => 'New message 2']);
        }
    }
};

$bot = new EzTG(array('token' => 'your bot token', 'callback' => $callback, 'objects' => false, 'allow_only_telegram' => true, 'throw_telegram_errors' => true, 'magic_json_payload' => false)); //don't enable magic_json_payload if u want telegramz response