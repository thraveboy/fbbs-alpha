import subprocess
hostname = subprocess.check_output('hostname').strip('\n')
print(hostname)
avail_space = subprocess.check_output("stat -f -c\"%a\" /", shell=True).strip('\n')
print(avail_space)
total_space = subprocess.check_output("stat -f -c\"%b\" /", shell=True).strip('\n')
print(total_space)
percentage_free = int((float(avail_space) / float(total_space)) * 100.0)
print(percentage_free)

api_command_str = "python fbbs_api.py " + "monitor_" + hostname + "_diskfree '" + str(percentage_free) + "%'"
print(api_command_str)
fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)

cpu_percentage = subprocess.check_output("top -bn3 | grep \"Cpu(s)\" | sed \"s/\\([0-9.]*\\) sy.*/\\1/\" | awk 'END {print $2}'", shell=True)
print(cpu_percentage) 
api_command_str = "python fbbs_api.py " + "monitor_"  + hostname + "_cpu '" + str(cpu_percentage) + "%'"
fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)

mem_used_percentage = subprocess.check_output("top -bn1 | grep \"KiB Mem\" | awk '{print ($5 / $3) * 100}' ", shell=True)
print(mem_used_percentage)
api_command_str = "python fbbs_api.py " + "monitor_" + hostname + "_memused '" + str(mem_used_percentage) + "%'"
fbbs_api_obj = subprocess.check_output(api_command_str, shell=True)
