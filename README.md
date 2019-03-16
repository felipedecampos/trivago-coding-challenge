# Coding challenge - Be the owner of a winery

## ** Test specification ** 
[Link of the test specification](https://github.com/felipedecampos/trivago-coding-challenge/tree/master/docs)

## ** Project BOARD (Kanban) **
[Link to Kanban](https://github.com/felipedecampos/trivago-coding-challenge/projects/1)

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

**Follow the steps bellow:**

1\)  2- Set up new Network
  - Type the network name \[example: trivago\]: 
    > trivago
  - Type an IP for the network: 
    > 180.12.0.0
  - You should see
    > Network trivago successfully set up

2\) 1- Show current Network 
  - You should see
    > Network set up: trivago > 180.12.0.0
    
3\) 3- Install project: Trivago 
  - Project name \[trivago-coding-challenge\]: 
    > trivago-coding-challenge 
  - Do you want to clone the project \(notice: This command will clone the project from a github repository\) y/n? \[n\]: 
    > n
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
  - Redis host \[180.12.0.5\]:
    > 180.12.0.5
  - Redis port \[63798\] (you must use 5 characters):
    > 63798
  - Redis data path \[/home/USER/Workspace/.redis/trivago-coding-challenge\]:
    > /home/$USER/Workspace/.redis/trivago-coding-challenge
  - Are you sure you want to install the project \[trivago-coding-challenge\] y/n? \[y\]:
    > y
  - Would you like to configure smtp \(this is required if you want to send emails\) y/n? \[n\]:
    > y
  - Encryption type \[tls\]: 
    > tls
  - SMTP host \[smtp.gmail.com\]: 
    > smtp.gmail.com
  - SMTP port \[587\]: 
    > 587
  - SMTP user \[user@gmail.com\]: 
    > your_email@gmail.com
  - SMTP password \[null\]:
    > your_password

**Click the link below to access the project:**

[http://trivago-coding-challenge.local](http://trivago-coding-challenge.local)

##### Note: 

The installer helper will install the project environment with docker-compose

#### Windows and Macbook:

All tests were made in Debian 9, I can't guarantee it will work on other operating systems

## ** Testing **

To test the application go to the project folder and run tests:
```shell
$ vendor/bin/phpunit
```

## ** Logs **

To “live” view the jobs log, run into container (php-fpm):
```shell
$ tail -f storage/logs/worker.log
```

To “live” view the crontab log, run into container (php-fpm):
```shell
$ tail -f storage/logs/crontab.log
```

To “live” view the application log, run into container (php-fpm):
```shell
$ tail -f storage/logs/laravel-DATE.log
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

## ** What has been done **
You will need to register on site to place orders.

Press the Register button on home page. Enter your name, e-mail address, password, and confirm you password – to confirm your password, you must enter the exact same password typed in the previous field. Click the Register button.

You will be taken to the index page. To place an order, click the button + Place order. Select one or more wines from the list. The options in green are the available wines of the day (just to make easier testing the application).

After selecting the wines, click Submit. You will be taken to the index page with your orders list. Your order status will be Open and each item status is displayed as Placed. Press F5 to refresh the page until your order status is updated to Closed. Jobs will be setup to be run every 5 seconds to make easier viewing jobs steps while testing the application.

In the first job, you will be assigned an available waiter to process your order and send it to the sommelier to check the availability of the wines.

In the last job, the waiter will deliver and close the order.

After delivery of the order, your order status will change to Closed and your items will be displayed as Delivered or Unavailable according to sommelier’s response.
