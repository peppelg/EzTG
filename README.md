# EzTG

⚡️ EzTG is a minimal async (& non-async) Telegram Bot Api Framework.
It's available for Python and PHP.

The methods are the same of the [Telegram Bot Api](https://core.telegram.org/bots/api), so you don't need to learn anything new.


  

---

## Index

*  [🌟 Getting started](#getting-started)

*  [📖 How to call a method](#calling-methods)

*  [ℹ️ Telegram help group](#help)


---
## Getting started
Choose the version you prefer:

- [PHP non-async](https://github.com/peppelg/EzTG/tree/master/PHP/Standard)
- [Python non-async](https://github.com/peppelg/EzTG/tree/master/Python/Standard)
- [Python async](https://github.com/peppelg/EzTG/tree/master/Python/Async)


## Calling methods
The methods are the same of the Telegram Bot Api. So, if you want to send a message, you will use the [sendMessage](https://core.telegram.org/bots/api#sendmessage) method. 
![Example](https://i.imgur.com/XiNHYU4.png)

`chat_id` and `text` are required. So...

Python: `bot.sendMessage(chat_id=1234, text='hi!')`

PHP: `$bot.sendMessage(['chat_id' => 1234, 'text' => 'hi!']);`

To find the current [chat id](https://core.telegram.org/bots/api#chat): `chat_id = update['message']['chat']['id']`

[[tutorial] How to send a photo using EzTG](https://youtu.be/l7dSa7KS1S0)

Take a look at the `example.py/example.php` file.


# Help

[EzTG and TGUserbot group](https://t.me/joinchat/HIyPnk3GQ7525LpP62yIWA)

 