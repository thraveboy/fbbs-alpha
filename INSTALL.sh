#!/bin/sh
echo 'sudo apt-get update'
sudo apt-get update
echo 'sudo apt-get install apache2 apache2-utils -y'
sudo apt-get install apache2 apache2-utils -y
echo 'sudo apt-get install php5 libapache2-mod-php5 -y'
sudo apt-get install php5 libapache2-mod-php5 -y
echo 'sudo apt-get install php5-sqlite -y'
sudo apt-get install php5-sqlite -y
echo 'sudo apt-get install sqlite3'
sudo apt-get install sqlite3
echo 'sudo cp * /var/www/html'
sudo cp * /var/www/html
echo 'sudo chmod a+wr /var/www/html/*'
sudo chmod a+wrx /var/www/html/*
echo 'sudo chmod a+wr /var/www/html/'
sudo chmod a+wrx /var/www/html/
echo 'sudo apache2ctl restart'
sudo apache2ctl restart
