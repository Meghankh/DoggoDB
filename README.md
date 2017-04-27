Doggo Daycare Database Code

Set Up:
=============
EC2: https://us-west-1.console.aws.amazon.com/ec2/v2/home?region=us-west-1#Instances:sort=instanceId

EC2 Public DNS: ec2-54-193-106-184.us-west-1.compute.amazonaws.com
	user: ubuntu or root
	keypair: doggo.pem
EC2 Public IP: 54.193.106.184

RDS: https://us-west-1.console.aws.amazon.com/rds/home?region=us-west-1

MySQL: doggo.cwmarjk4rlar.us-west-1.rds.amazonaws.com
Port: 3306
User: root
DB: DoggoDB

Description:
==============
This repository is just to hold all the code necessary for the apache2 server that will serve as a middle man between the DoggoDB MySQL Database and the Doggo App.

The app will send http requests to this server to 54.193.106.184/DoggoDB/doggoAPI.php with the appropriate "action" and other parameters. The response will be json compliant.

Installation:
===============
sudo apt-get install apache2
sudo apt-get install php
sudo apt-get install php-mysql
git clone repo to /var/www/html
cp php.ini to /etc/php/0.7/apache2/php.ini
/etc/init.d/apache2 restart

