# EzTG PHP non-async

## Install EzTG
- PHP is required
- Copy `EzTG.php` and `example.php`
- Enter your bot token in `example.php`

## Run your bot
You can use long polling or webhook. If you want to use webhook, you will need a webserver (or a hosting) with PHP and HTTPS.
#### Long polling
`php example.php`
#### Webhook
To set the webhook, open `https://api.telegram.org/botYOUR_TOKEN_HERE/setWebhook?url=https://yoursite.com/EzTG/example.php` in your browser. You must replace `YOUR_TOKEN_HERE` with your bot token and `yoursite.com` with your site's domain.


## EzTG constructor settings
|                       	|                                 	|                                                                                                                                                    	|
|-----------------------	|---------------------------------	|----------------------------------------------------------------------------------------------------------------------------------------------------	|
| endpoint              	| string:`https://api.telegram.org` 	| Telegram bot api endpoint.                                                                                                                         	|
| token                 	| string                          	| Your bot token.                                                                                                                                    	|
| callback              	| callable                        	| Function to call when the bot receives an update.                                                                                                  	|
| objects               	| boolean:`true`                    	| Use objects. If false, an array will be returned (json_decode 1).                                                                                  	|
| allow_only_telegram   	| boolean:`true`                    	| [WEBHOOK] If true, only requests coming from Telegram's servers will be accepted.                                                                  	|
| throw_telegram_errors 	| boolean:`true`                    	| Throws an EzTGException if an error occurs.                                                                                               	|
| magic_json_payload    	| boolean:`false`                   	| [WEBHOOK] Answer using json payload to the Telegram request. If set to true, the Telegram response of a called method will be null, so be careful. 	|
There's also a second parameter (base), if set to true, EzTG will not handle updates automatically. You can use it when you don't need to receive updates.

p.s You can rename `example.php` to `bot.php`