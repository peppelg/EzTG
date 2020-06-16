# EzTG Python async

## Install EzTG
- Python3 is required
- Copy `EzTG.py`, `example.py` and `requirements.txt`
- Install the requirements (`asyncio`, `aiohttp`): `pip install -r requirements.txt`
- Enter your bot token in `example.py`

## Run your bot
Only long polling is supported: `python example.py`

## EzTG constructor settings
|                       	|                                 	|                                                                                     	|
|-----------------------	|---------------------------------	|-------------------------------------------------------------------------------------	|
| token                 	| string                          	| Your bot token.                                                                     	|
| callback              	| callable                        	| Function to call when there's a new update.                                         	|
| throw_telegram_errors 	| boolean:`true`                    	| Throws an APIException if an error occurs.                                          	|
| endpoint              	| string:`https://api.telegram.org` 	| Telegram bot api endpoint.                                                          	|
| offset                	| int:`-1`                          	| First offset.                                                                       	|
| timeout               	| int:`10`                          	| getUpdates timeout.                                                                 	|