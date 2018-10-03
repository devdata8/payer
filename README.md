Project "Payer"

Description "Payer system supported simple interface for creating users pays and payment processing"

Project created with help PHP 7.1, PostgreSQL, Yii 2 Advanced Template 

Links to GitHub project: https://github.com/devdata8/payer

-------------------------

Installation Instruction:

1. Clone project to server:
    git clone https://github.com/devdata8/payer.git payer

2. Check requirements for web server and install/update needed libs:
    a. cd /www 
    b. php requirements.php	

3. Configure Web Server virtual host. WebRoot folder for virtual host "www/frontend/web/".
    For Example my config file for Apache 2.4:  
    <VirtualHost *:80>
            ServerAlias payer.lo
            DocumentRoot /var/www/payer/www/frontend/web/
            ServerAdmin rrsrusakov@gmail.com
            LogLevel warn
            ErrorLog /var/log/apache2/payer_error.log
            CustomLog /var/log/apache2/payer_access.log combined
            <Directory /var/www/payer/www/frontend/web>
                    Options Indexes FollowSymLinks MultiViews
                    AllowOverride all
                    Order deny,allow
                    allow from 127.0.0.1
                    Require all granted
            </Directory>
    </VirtualHost>

4. Update composer modules:
    a. cd /www
    b. composer update

5. Init YII2 project 
    a. cd /www
    b. php init
    b. Select "[1] Production"
    b. Select "yes"

6 .Prepare Database and user PostreSQL for project:
    a. Commands for Ubuntu:
        > sudo -u postgres psql
        > CREATE DATABASE test_database;
        > CREATE USER test_user WITH password 'qwerty';
        > GRANT ALL ON DATABASE test_database TO test_user;

7. Set Database and User parameters to config file of project:
    a. modify connect string, user and password in file: /www/common/config/main-local.php
    b. My Example:
    ...
    'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'pgsql:host=localhost;dbname=test_database',
                'username' => 'test_user',
                'password' => 'qwerty',
                'charset' => 'utf8',
            ],
    ...
            
8. Migrate tables and test data of users to project Database: 
    a. cd /www
    b. php yii migrate
    b. Select "yes"
    Note:
    c. If you need drop all tables, you can down migrate by: "php yii migrate/down 1"

9. Set start every an hour script for payments processing. It works from console interface.
    a. By hand you can execute script by command "php yii processing"
    b. For exec script  every an hour you have to add crone command as example:
    * */1 * * * /usr/bin/php /var/www/payer/www/yii processing > /var/log/crone/payer_log.log
    c. Same cron command but with disabled logs:
    * */1 * * * /usr/bin/php /var/www/payer/www/yii processing > /dev/null

----------------

User Instruction:

1. After install you can visit web site with next data & functions:
    a. By default system has SignUp (/index.php?r=site%2Fsignup) and SignIn (/index.php?r=site%2Flogin) interfaces.
    b. After auth user can visit 2 pages: list payers with last payment (/index.php?r=site%2Fpayers) 
        and pay interface (/index.php?r=site%2Fpay)
    c. After migration DB system isset 3 test users:
    c. 1. login "ros" password "1672943" - balance user - 900
    c. 2. login "user1" password "test7854" - balance user - 100
    c. 3. login "user2" password "test7854" - balance user - 0