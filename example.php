<?php

require_once __DIR__.'/vendor/autoload.php';


$callback = function($update, $EzTG){
	if(isset($update->message->text) and $update->message->text == '/start') $EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => "/inline\n/keyboard\n/hidekb"]);
	if(isset($update->message->text) and $update->message->text == '/inline'){
    	$keyboard = $EzTG->newKeyboard('inline')
	    	->add('example', 'callback data')
	    	->add('example 2', 'callback data 2')
	    	->newline()
	    	->add('example 3', 'https://www.google.com')
	    	->done();
    	$EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => 'wewe', 'reply_markup' => $keyboard]);
  	}
  	if(isset($update->message->text) and $update->message->text == '/keyboard'){
    	$keyboard = $EzTG->newKeyboard('keyboard')
	    	->add('example')
	    	->add('example 2')
	    	->newline()
	    	->add('example 3')
	    	->done();
    	$EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => 'wewe', 'reply_markup' => $keyboard]);
  	}
  	if(isset($update->message->text) and $update->message->text == '/hidekb'){
    	$keyboard = $EzTG->newKeyboard('remove')->done();
    	$EzTG->sendMessage(['chat_id' => $update->message->chat->id, 'text' => ',.,.', 'reply_markup' => $keyboard]);
  	}
};

$EzTG = new peppelg\EzTG\Main(['token' => 'token', 'callback' => $callback]);