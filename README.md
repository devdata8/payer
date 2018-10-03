Project "Payer"

Description "Payer system supported simple simple inteface for creating users pays and payment processing"

Project created with help PHP, PostgreSQL, Yii 2 Advanced Template 

Links to GitHub project: https://github.com/devdata8/payer

Instruction:

1. git clone https://github.com/devdata8/payer.git payer

2. Check requirements for web server
    php requirements.php	

2. Configure Web Server
    <VirtualHost *:80>
            ServerAlias lpayer.lo
    
            DocumentRoot /var/www/payer/www/frontend/web/
            ServerAdmin rrsrusakov@gmail.com
    
            LogLevel warn
            ErrorLog /var/log/apache2/lpayer_error.log
            CustomLog /var/log/apache2/lpayer_access.log combined
    
            <Directory /var/www/payer/www/frontend/web>
                    Options Indexes FollowSymLinks MultiViews
                    AllowOverride all
                    Order deny,allow
                    allow from 127.0.0.1
                    Require all granted
            </Directory>
    </VirtualHost>


3. composer update

4. php init
[1] Production
yes

5. php yii migrate
yes

6 .Prepare Database PostreSql:
sudo -u postgres psql
    Create database and user
CREATE DATABASE test_database;
CREATE USER test_user WITH password 'qwerty';
GRANT ALL ON DATABASE test_database TO test_user;

7. Set Database parameters to config file of project:
    /www/common/config/main-local.php
    like example:
    'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'pgsql:host=localhost;dbname=payer',
                'username' => 'payer',
                'password' => 'payer7854',
                'charset' => 'utf8',
            ],