import typing
import requests
import urllib.parse
import warnings
import json


class APIException(Exception):
    pass


class EzTG:
    def __init__(self, token: str, callback: typing.Callable, base=False, throw_telegram_errors=True, endpoint='https://api.telegram.org', offset: int = -1, timeout: int = 10):
        self.token = token
        self.callback = callback
        self.endpoint = endpoint
        self.offset = offset
        self.timeout = timeout
        self.throw_telegram_errors = throw_telegram_errors
        if base == False:  # base serve x non fare getupdates e wuindi se non serve pigliare mex
            self._getUpdates()

    def _getUpdates(self):
        updates = {}
        while True:
            try:
                updates = self._telegramRequest(
                    'getUpdates', {'offset': self.offset, 'timeout': self.timeout})
            except Exception as e:
                warnings.warn(str(e))
            for update in updates:
                self.offset = update['update_id'] + 1
                self.processUpdate(update)

    def processUpdate(self, update):
        return self.callback(self, update)

    def _telegramRequest(self, method, params={}):
        r = requests.post(self.endpoint + '/bot' + self.token +
                          '/' + urllib.parse.quote(method), data=params)
        response = r.json()
        if response['ok'] == True and 'result' in response:
            return response['result']
        else:
            if response['ok'] == False and 'description' in response:
                if self.throw_telegram_errors:
                    raise APIException(response['description'])
                else:
                    warnings.warn(response['description'])
            else:
                if self.throw_telegram_errors:
                    raise APIException('Unknown error')
                else:
                    warnings.warn('Unknown error')
            return response

    def __getattr__(self, name):
        def function(**kwargs):
            return self._telegramRequest(name, kwargs)
        return function


class Keyboard:
    def __init__(self, type='keyboard', rkm={'resize_keyboard': True, 'keyboard': []}):
        self.line = []
        self.type = type
        if type == 'inline':
            rkm = {'inline_keyboard': []}
        self.keyboard = rkm

    def add(self, text: str, callback_data=None, type='auto'):
        if self.type == 'inline':
            if callback_data == None:
                callback_data = text
            if type == 'auto':
                if callback_data.startswith('http://') or callback_data.startswith('https://'):
                    type = 'url'
                else:
                    type = 'callback_data'
            self.line.append(
                {'text': text, type: callback_data})
        else:
            self.line.append(text)
        return self

    def newLine(self):
        if self.type == 'inline':
            self.keyboard['inline_keyboard'].append(self.line)
        else:
            self.keyboard['keyboard'].append(self.line)
        self.line = []
        return self

    def done(self):
        self.newLine()
        if self.type == 'remove':
            return '{"remove_keyboard": true}'
        else:
            return json.dumps(self.keyboard)

    def __str__(self):
        return self.done()
