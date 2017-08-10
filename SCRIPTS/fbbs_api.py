import requests
import json
import sys

class FbbsApi:
    """Class object for interacting with PiDash API"""
    def __init__(self, in_host="http://localhost/pidash-api.php", in_username="SysOp", in_password="sysop", in_token=""):
        self.host = in_host
        self.username = in_username
        self.password = in_password
        self.token = in_token
        self.command = ""
    def set_command(self, command):
        self.command = command
    def send(self):
        token_request = requests.post(self.host, data={'username': self.username, 'password': self.password})
        json_decoder = json.JSONDecoder()
        decoded_json = json_decoder.decode(token_request.text)
        if "user_created" in decoded_json:
            token_request = requests.post(self.host, data={'username': self.username, 'password': self.password})
            decoded_json = json_decoder.decode(token_request.text)
        if "token" in decoded_json:
            self.token = decoded_json["token"]
            send_command = requests.post(self.host, data={'username': self.username, 'token': self.token, 'command' : self.command})
            print(send_command.text)

if (len(sys.argv) > 1):
    api_test_obj = FbbsApi()
    print('command:' + ' '.join(sys.argv[1:]))
    api_test_obj.set_command(' '.join(sys.argv[1:]))
    api_test_obj.send()



