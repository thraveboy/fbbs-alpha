## Synopsis

Fury Bulletin Board System (fbbs).

## Installation

Install Apache/Php/SQLite (move to mysql soonish)

Update apt-get which will have apt-get retrieve any updated information
on the packages (Apache, PHP, Sqlite, etc) from the web so they will be
installed correctly:

sudo apt-get update

Install Apache (the web server that will be your interface for
handling web requests to your machine, running FBBS to process the
request, and then send back the response to the person who requested it):

sudo apt-get install apache2 apache2-utils -y

Install PHP and Apache module so when Apache gets the requests from the 
web it can fire off and run your php programs to process the data and
then give it back to Apache which will send it back out:

sudo apt-get install php5 libapache2-mod-php5 -y

Install SQLite (the database) module for PHP so that when PHP is handed
the web request from apache, it can used SQLite to store data on disk
between requests and forever if you like:

sudo apt-get install php5-sqlite -y 

Get sqlite3 command line tool which allows you to look at the data stored
in the SQLite database and make changes. When you make changes here, then
when PHP looks at the data again it will see the changes you made:

sudo apt-get install sqlite3

From base fbbs directory, copy files to /var/www/html:

cp * /var/www/html

Go to the directory to modify permissions:

cd /var/www/html

Change access to files apache, php, and sqlite can access them:

sudo chmod a+wr *

### To have 404 direct to custom_404 file change the add the following to the
### apache config"  /etc/apache2/apache2.conf

sudo nano /etc/apache2/apache2.conf

and add this line to the end of the file and save it:

ErrorDocument 404 /custom_404.html

Now, restart the Apache web server to pick up the changes:

sudo apache2ctl restart


### If want to create DBs by hand, instead of using default dbs. (must also change access with chmod as in above).


Create initial main public db:

touch fbbs.db
> 
PRAGMA journal_mode=WAL;

Create users table in fbbs.db in sqlite3:

sqite3 fbbs-user.db
> 
PRAGMA journal_mode=WAL;
CREATE TABLE users(id INTEGER PRIMARY KEY ASC, username TEXT UNIQUE NOT NULL, password TEXT NOT NULL, timestamp INTEGER NOT NULL);
CREATE INDEX username_idx ON users(username);
CREATE TABLE auth_tokens(username TEXT PRIMARY KEY NOT NULL, token TEXT NOT NULL, expire TEXT NOT NULL, timestamp INTEGER NOT NULL);
CREATE TABLE user_auth_log(id INTEGER PRIMARY KEY NOT NULL, username TEXT NOT NULL, token TEXT NOT NULL, timestamp INTEGER NOT NULL);

Create private database for modules in sqlite:

sudo touch fbbs-private.db; sudo chmod a+wr fbbs-private.db
>
CREATE TABLE table_write_auth(id INTEGER PRIMARY KEY ASC, tablename TEXTNOT NULL, username TEXT NOT NULL, timestamp INTEGER NOT NULL);
CREATE INDEX table_write_auth_idx ON table_write_auth(tablename);
 


