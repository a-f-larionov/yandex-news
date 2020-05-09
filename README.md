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

