# Coding challenge - Be the owner of a winery

## ** Introduction ** 
[Link to my introduction](https://github.com/felipedecampos/trivago-coding-challenge/tree/master/docs/INTRODUCTION.md)

## ** Test specification ** 
[Link to the test specification](https://github.com/felipedecampos/trivago-coding-challenge/tree/master/docs)

## ** Project BOARD (Kanban) **
[Link to Kanban](https://github.com/felipedecampos/trivago-coding-challenge/projects/1)

## ** What has been done **

First you will need to install the application environment with docker-compose

[Go to How to install the project environment](https://github.com/felipedecampos/trivago-coding-challenge#-how-to-install-the-project-environment-)

The script didn't work?

[Go to How to manually install the project environment](https://github.com/felipedecampos/trivago-coding-challenge#the-script-didnt-work)

Then you will need to register on site to place orders.

Press the Register button on home page. Enter your name, e-mail address, password, and confirm your password – to confirm your password, you must enter the exact same password typed in the previous field. Click the Register button.

You will be taken to the index page. To place an order, click the button + Place order. Select one or more wines from the list. The options in green are the available wines of the day (just to make easier testing the application).

After selecting the wines, click Submit. You will be taken to the index page with your orders list. Your order status will be Open and each item status is displayed as Placed. Press F5 to refresh the page until your order status is updated to Closed. Jobs will be setup to be run every 20 seconds to make easier viewing jobs steps while testing the application.

In the first job, you will be assigned an available waiter to process your order and send it to the sommelier to check the availability of the wines.

In the last job, the waiter will deliver and close the order.

After delivery of the order, your order status will change to Closed and your items will be displayed as Delivered or Unavailable according to sommelier’s response.

## ** Testing **

To test the application go to the **project folder** and run the tests:

```shell
$ vendor/bin/phpunit
```

## ** PHP Standards Recommendations **

To validate the code for consistency with a coding standard go to the **project folder** and run the commands:

**PSR-1**
```shell
$ vendor/bin/phpcs --standard=PSR1 --extensions=php --ignore=*/database/*,*/resources/*,*/storage/*,*/vendor/*,*/public/index.php,*/tests/bootstrap.php,*/bootstrap/cache/* .
```

**PSR-2**

```shell
$ vendor/bin/phpcs --standard=PSR2 --extensions=php --ignore=*/database/*,*/resources/*,*/storage/*,*/vendor/*,*/public/index.php,*/tests/bootstrap.php,*/bootstrap/cache/* .
```

**PSR-12**

```shell
$ vendor/bin/phpcs --standard=PSR12 --extensions=php --ignore=*/database/*,*/resources/*,*/storage/*,*/vendor/*,*/public/index.php,*/tests/bootstrap.php,*/bootstrap/cache/* .
```

## ** Logs **

**To monitor the logs, follow the steps bellow:**

In the **project folder** run:  

```shell
$ cd docker && ./run.sh
```

**Enter the options bellow in the menu:**

When asked to "Type the service number you want to use:", enter option: **4- Enter into container: Trivago**
> 4

When asked to "Type the container number you want to enter:", enter option: **2- PROJECT-NAME-php-fpm**
> 2

To view the application log in real time, run into container (PROJECT-NAME-php-fpm):
```shell
$ tail -f storage/logs/application.log
```

To view the application queries log in real time, run into container (PROJECT-NAME-php-fpm):
```shell
$ tail -f storage/logs/queries.log
```

To view the ran jobs log in real time, run into container (PROJECT-NAME-php-fpm):
```shell
$ tail -f storage/logs/worker.log
```

To view view the crontab log in real time, run into container (*php-fpm):
```shell
$ tail -f storage/logs/crontab.log
```

## ** Wines catalog **

Wines catalog according to RSS link will be updated everyday at 9AM with crontab set up. If you want to manually update all wines according to RSS link, run into container (php-fpm):
```shell
$ php artisan wine-spectator:watch all
```

If you want to manually update only the wines available today according to RSS link, run into container (php-fpm):
```shell
$ php artisan wine-spectator:watch
```

## ** How to install the project environment **

#### Linux:

#### requirements:

- **docker** version **17.05.0-ce**
- **docker-compose** version **1.19.0**
- **git** version **2.7.4**

To know your docker version run:

```shell
$ docker -v
```

To know your docker-composer version run:

```shell
$ docker-compose -v
```

To know your git version run:

```shell
$ git --version
```

**Clone the project**

```shell
$ git clone https://github.com/felipedecampos/trivago-coding-challenge.git
```

In the **project folder** run:  

```shell
$ cd docker && ./run.sh
```

**Enter the options bellow in the menu:**

When asked to "Type the service number you want to use:", enter option: **2- Set up new Network** 
> 2

  - Type the network name \[example: trivago\]: 
    > trivago

  - Type an IP for the network: 
    > 180.12.0.0

You should see: **Network trivago successfully set up**

Press Enter to go back to the menu

When asked to "Type the service number you want to use:", enter option: **1- Show current Network**
> 1

You should see: **Network set up: trivago > 180.12.0.0**

Press Enter to go back to the menu

When asked to "Type the service number you want to use:", enter option: **3- Install project: Trivago **
> 3

**Note: If you want to insert the \[default value\], just press the Enter button**

  - Project name \[trivago-coding-challenge\]: 
    > trivago-coding-challenge 
  - Project PATH \[/home/USER/Workspace/trivago-coding-challenge\]: 
    > /home/$USER/Workspace/trivago-coding-challenge 
  - App url \[trivago-coding-challenge.local\]: 
    > trivago-coding-challenge.local
  - Nginx host \[180.12.0.2\]: 
    > 180.12.0.2 
  - PHP host \[180.12.0.3\]:
    > 180.12.0.3
  - Database connection \[pgsql\]:
    > pgsql
  - Database host \[180.12.0.4\]:
    > 180.12.0.4
  - Database port \[54329\] (you must use 5 characters):
    > 54329
  - Database user \[trivago\]:
    > trivago
  - Database password \[165497381546982\]:
    > 165497381546982
  - Database name \[trivago-coding-challenge\]:
    > trivago-coding-challenge
  - Postgres data path \[/home/USER/Workspace/.db/postgres/trivago-coding-challenge\]:
    > /home/$USER/Workspace/.db/postgres/trivago-coding-challenge
  - Are you sure you want to install the project \[trivago-coding-challenge\] y/n? \[y\]:
    > y
  - Do you want to add the project host on your /etc/hosts file? \(warning: This command needs the sudo privilege\) y\/n? \[n\]:
    > y

**Click the link below to access the project:**

[http://trivago-coding-challenge.local](http://trivago-coding-challenge.local)

##### Note: 

The installer helper will install the project environment with docker-compose

#### Windows and Macbook:

All tests were made in Debian 9, I can't guarantee it will work on other operating systems

#### The script didn't work?

**Follow the steps below to install the project manually:**

In the **project folder** run:

```shell
yes | cp -i docker/.env.example docker/.env
yes | cp -i docker/environments/trivago/.env.example docker/environments/trivago/.env
yes | cp -i docker/environments/trivago/docker-compose.yml.example docker/environments/trivago/docker-compose.yml
yes | cp -i docker/environments/trivago/nginx/nginx.conf.example docker/environments/trivago/nginx/nginx.conf
yes | cp -i docker/environments/trivago/php-fpm/overrides.ini.example docker/environments/trivago/php-fpm/overrides.ini
yes | cp -i docker/environments/trivago/php-fpm/laravel-cron.example docker/environments/trivago/php-fpm/laravel-cron
yes | cp -i docker/environments/trivago/php-fpm/supervisord.conf.example docker/environments/trivago/php-fpm/supervisord.conf
yes | cp -i .env.example .env
```

Please, fill the variables of the files created above, follow the examples bellow:

**Note: Make sure to replace all variables defined with curly brackets {VARIABLE}**

**docker/.env**:

- replace the {NETWORK_NAME} variable to:
    > trivago
- replace the {NETWORK_IP} variable to:
    > 180.12.0.0
- replace the {DOCKER_PROJECT_PATH} variable to:
    > {PROJECT_FOLDER}/docker

**docker/environments/trivago/.env**:

- Replace the {PROJECT_NAME} variable to:
    > trivago-coding-challenge
- Replace the {PROJECT_PATH} variable to:
    > /home/{USER}/Workspace/trivago-coding-challenge
- Replace the {APP_URL} variable to:
    > trivago-coding-challenge.local
- Replace the {NGINX_HOST} variable to:
    > 180.12.0.2
- Replace the {PHP_HOST} variable to:
    > 180.12.0.3
- Replace the {POSTGRES_HOST} variable to:
    > 180.12.0.4
- Replace the {POSTGRES_USER} variable to:
    > trivago
- Replace the {POSTGRES_PASSWORD} variable to:
    > 165497381546982
- Replace the {POSTGRES_DB} variable to:
    > trivago-coding-challenge
- Replace the {POSTGRES_PORT} variable to:
    > 54329
- Replace the {POSTGRES_PATH} variable to:
    > /home/{USER}/Workspace/.db/postgres/trivago-coding-challenge

**docker/environments/trivago/nginx/nginx.conf**:

- Replace the {APP_URL} variable to:
    > trivago-coding-challenge.local
- Replace the {PROJECT_PATH} variable to:
    > /home/{USER}/Workspace/trivago-coding-challenge

**docker/environments/trivago/php-fpm/overrides.ini**:

- Replace the ${NGINX_HOST} variable to:
    > 180.12.0.2

**docker/environments/trivago/php-fpm/laravel-cron**:

- Replace the {PROJECT_NAME} variable to:
    > trivago-coding-challenge

**docker/environments/trivago/php-fpm/supervisord.conf**:

- Replace all {PROJECT_NAME} variables to:
    > trivago-coding-challenge

**.env**:

- Fill the env file with the data bellow:
    > APP_NAME=trivago-coding-challenge
    
    > APP_URL=http://trivago-coding-challenge.local
    
    > DB_CONNECTION=pgsql
    
    > DB_HOST=180.12.0.4
    
    > DB_PORT=5432
    
    > DB_DATABASE=trivago-coding-challenge
    
    > DB_USERNAME=trivago
    
    > DB_PASSWORD=165497381546982

In the **project folder** run:

```shell
docker-compose -f docker/environments/trivago/docker-compose.yml --project-name "trivago-coding-challenge" up -d --force-recreate --build --remove-orphans
docker exec --user docker trivago-coding-challenge-php-fpm /bin/bash -c "cd trivago-coding-challenge && composer install"
docker exec --user docker trivago-coding-challenge-php-fpm /bin/bash -c "cd trivago-coding-challenge && php artisan key:generate"
chmod 777 $(find ../storage/ -not -name ".gitignore")
chmod 777 $(find ../bootstrap/cache/ -not -name ".gitignore")
docker exec --user docker trivago-coding-challenge-php-fpm /bin/bash -c "cd trivago-coding-challenge && php artisan migrate:refresh --seed"
docker exec --user docker trivago-coding-challenge-php-fpm /bin/bash -c "cd trivago-coding-challenge && php artisan wine-spectator:watch all"
sudo -- sh -c -e "echo '180.12.0.2\ttrivago-coding-challenge.local' >> /etc/hosts";
```

## ** How to uninstall the project environment **

In the **project folder** run:

```shell
$ cd docker && ./run.sh
```

**Enter the options bellow in the menu:**

When asked to "Type the service number you want to use:", enter option: **5- Uninstall project: Trivago** 
> 5

When asked to "Are you sure you want to uninstall the project PROJECT-NAME (notice: There is no way to undo this) y/n? [n]:", enter option: **y**
> y

When asked to "Do you want to remove the project host on your /etc/hosts file? (warning: This command needs the sudo privilege) y/n? [n]:", enter option: **y**
> y

When asked to "Do you want to remove the project repository (notice: There is no way to undo this) y/n? [n]:", enter option: **y**
> y