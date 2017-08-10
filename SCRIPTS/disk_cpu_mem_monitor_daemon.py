import subprocess
import time

hostname = subprocess.check_output('hostname').strip('\n')
total_space = subprocess.check_output("stat -f -c\"%b\" /", shell=True).strip('\n')

while 1:
    avail_space = subprocess.check_output("stat -f -c\"%a\" /", shell=True).strip('\n')
    percentage_free = int((float(avail_space) / float(total_space)) * 100.0)
    api_command_str = "python fbbs_api.py monitor_" + hostname + "_diskfree '" + str(percentage_free) + "%'"
    fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
    cpu_percentage = subprocess.check_output("top -bn3 | grep \"Cpu(s)\" | sed \"s/\\([0-9.]*\\) sy.*/\\1/\" | awk 'END {print $2}'", shell=True)
    api_command_str = "python fbbs_api.py " + "monitor_"  + hostname + "_cpu '" + str(cpu_percentage) + "%'"
    fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
    mem_used_percentage = subprocess.check_output("top -bn1 | grep \"KiB Mem\" | awk '{print ($5 / $3) * 100}' ", shell=True)
    api_command_str = "python fbbs_api.py " + "monitor_" + hostname + "_memused '" + str(mem_used_percentage) + "%'"
    fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
    time.sleep(10)
