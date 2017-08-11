import subprocess
import string
import time
import sys

currencies = ['BTC', 'BCC', 'ETH', 'STRAT', 'XMR', 'OMG', 'LTH', 'GNT', 'WAVES', 'QTUM', 'PIVX', 'SC', 'NEO', 'LTC', 'DASH', 'PAY', 'GBYTE', 'FCT', 'ETC']     
update_rate_mins = 5
if (len(sys.argv) > 1):
  update_rate_mins = int(float(sys.argv[1]))

while 1:
  ticker_values = subprocess.check_output('curl https://www.worldcoinindex.com/ | sed -f coin-scrap.sed | sed -f sed-clean.sed | grep Ticker', shell=True)
  ticker_value_array = string.split(ticker_values,'\n')
  ticker_values_in_array = []
  for ticker_value in ticker_value_array:
    print(ticker_value)
    ticker_values_in_array.append(string.split(ticker_value,' '))
  updated_values = 1
  for ticker_value_in_array in ticker_values_in_array:
      try:
        if (ticker_value_in_array[0].split(":")[1] in currencies):
          api_command_str = "python fbbs_api.py '" + str(ticker_value_in_array[0]) + ":" + str(update_rate_mins) + "min'"
          print(api_command_str)
          fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
          ticker_value = str(ticker_value_in_array[1])
          api_command_str = "python fbbs_api.py '" + str(ticker_value_in_array[0]) + ":" + str(update_rate_mins) + "min' " + str(ticker_value)
          print(api_command_str)
          fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
          updated_values += 1
      except:
        pass
  api_command_str = "python fbbs_api.py 'Ticker:CryptoUpdates" + ":" + str(update_rate_mins) + "min'"
  print(api_command_str)
  fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
  api_command_str = "python fbbs_api.py 'Ticker:CryptoUpdates" + ":" + str(update_rate_mins) + "min' " + str(updated_values)
  print(api_command_str)
  fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)

  time.sleep(60 * update_rate_mins)
