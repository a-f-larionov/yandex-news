# Project News-Capture

# Setup server

Ubuntu

    apt update
    apt upgrade
MySql

    apt install mysql-server
    mysql_secure_installation
Nginx

    apt install nginx
PHP

    apt install php7.2
    apt install php-fpm7.2   
    apt install php-mysql

# Configure
Nginx
 
 @see nginx.conf
Mysql

    CREATE USER 'alor'@'%' IDENTIFIED BY 'alor';
    GRANT ALL PRIVILEGES ON alor.* TO 'alor'@'%';
    FLUSH PRIVILEGES;


# Installation

Worked on Ubuntu 16.04 Xenial Trust

To start server with OS

1 copy ./other/daemon.files/init.d/node-framework to /etc/init.d/node-framework
2 sudo chmod 755 /etc/init.d/node-framework
3 sudo chown root:root /etc/init.d/node-framework
4 sudo update-rc.d node-framework defaults
5 sudo update-rc.d node-framework enable
6 sudo service node-framework start

You can rename node-framework to your project name and then edit file /etc/init.d/node-framework,
replace all 'node-framework' to your project name.

7 copy ./other/daemon.files/nginx_sites-enabled/node-framework.conf to /etc/nginx/sites-available
8 sudo ln -s /etc/nginx/sites-available/node-framework.conf /etc/nginx/sites-enabled/node-framework.conf
9 nginx -t # for test
10 systemctl restart nginx

nginx_sites-enabled - /etc/nginx/sites-enabled/