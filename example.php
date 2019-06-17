<?php
require_once 'EzTG.php';
$callback = function ($update, $EzTG) {
    if (isset($update->message->text) and $update->message->text === '/start') {
        $EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => "/inline\n/keyboard\n/hidekb"]);
    }
    if (isset($update->message->text) and $update->message->text === '/inline') {
        $keyboard = $EzTG->newKeyboard('inline')
    ->add('example', 'callback data')
    ->add('example 2', 'callback data 2')
    ->newline()
    ->add('example 3', 'https://www.google.com')
    ->done();
        $EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => 'wewe', 'reply_markup' => $keyboard]);
    }
    if (isset($update->message->text) and $update->message->text === '/keyboard') {
        $keyboard = $EzTG->newKeyboard('keyboard')
    ->add('example')
    ->add('example 2')
    ->newline()
    ->add('example 3')
    ->done();
        $EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => 'wewe', 'reply_markup' => $keyboard]);
    }
    if (isset($update->message->text) and $update->message->text === '/hidekb') {
        $keyboard = $EzTG->newKeyboard('remove')->done();
        $EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => ',.,.', 'reply_markup' => $keyboard]);
    }
    if (isset($update->callback_query->data) and $update->callback_query->data === 'callback data') {
        $EzTG->answerCallbackQuery(['callback_query_id' => $update->callback_query->id, 'text' => 'example #1']);
        $keyboard = $EzTG->newKeyboard('inline')
    ->add('example 2', 'callback data 2')
    ->done();
        $EzTG->editMessageText(['chat_id' => $update->callback_query->from->id, 'message_id' => $update->callback_query->message->message_id, 'text' => 'New message', 'reply_markup' => $keyboard]);
    }
    if (isset($update->callback_query->data) and $update->callback_query->data === 'callback data 2') {
        $EzTG->answerCallbackQuery(['callback_query_id' => $update->callback_query->id, 'text' => 'example #2', 'show_alert' => true]);
        $EzTG->editMessageText(['chat_id' => $update->callback_query->from->id, 'message_id' => $update->callback_query->message->message_id, 'text' => 'New message']);
    }
};

$EzTG = new EzTG(array('token' => 'token', 'callback' => $callback, 'allow_only_telegram' => true, 'throw_telegram_errors' => true, 'magic_json_payload' => false)); //don't enable magic_json_payload if u want telegramz response