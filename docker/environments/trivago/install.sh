#!/usr/bin/env bash

if source ././.env; then
    if [ "$NETWORK_NAME" = "{NETWORK_NAME}" ] || [ "$NETWORK_IP" = "{NETWORK_IP}" ]; then
        echo -e "There isn't any Network set up yet! \n"
        read -p ''
        exit 0
    fi
else
    clear
    echo -e "There isn't any Network set up yet! \n"
    read -p ''
    exit 0
fi

read -p "Project name [trivago-coding-challenge]: " projectname
if [ -z "$projectname" ]; then
    projectname="trivago-coding-challenge"
fi

r_projectname="$(echo $projectname | sed -e 's/\(.*\)/\L\1/' | awk '{$1=$1};1' | sed -e 's/[[:space:]]\+/\-/g')"

echo -e "\n\e[32m$r_projectname\e[0m\n"

read -p "Do you want to clone the project (notice: This command will clone the project from a github repository) y/n? [n]: " yn
if [[ "$yn" = "y" ]]; then

    read -p "Where do you want to clone the project [/home/$USER/Workspace/$r_projectname]: " projectpath
    if [[ -z "$projectpath" ]]; then
        projectpath="/home/$USER/Workspace/$r_projectname"
    fi

    git clone https://github.com/felipedecampos/trivago-coding-challenge.git $projectpath

else

    read -p "Project PATH [/home/$USER/Workspace/$r_projectname]: " projectpath
    if [[ -z "$projectpath" ]]; then
        projectpath="/home/$USER/Workspace/$r_projectname"
    fi

fi

r_projectpath=${projectpath////\\/}

networkipbase=$(cut -d '.' -f 1,2,3 <<<"$NETWORK_IP")

read -p "App url [$r_projectname.local]: " appurl
if [ -z "$appurl" ]; then
    appurl="$r_projectname.local"
fi

read -p "Nginx host [$networkipbase.2]: " nginxhost
if [[ -z "$nginxhost" ]]; then
    nginxhost="$networkipbase.2"
fi

read -p "PHP host [$networkipbase.3]: " phphost
if [[ -z "$phphost" ]]; then
    phphost="$networkipbase.3"
fi

read -p "Database connection [pgsql]: " dbconnection
if [ -z "$dbconnection" ]; then
    dbconnection="pgsql"
fi

read -p "Database host [$networkipbase.4]: " dbhost
if [ -z "$dbhost" ]; then
    dbhost="$networkipbase.4"
fi

read -p "Database port [54329] (you must use 5 characters): " dbport
if [ -z "$dbport" ]; then
    dbport="54329"
fi

read -p "Database user [trivago]: " dbuser
if [ -z "$dbuser" ]; then
    dbuser="trivago"
fi

read -p "Database password [165497381546982]: " dbpass
if [ -z "$dbpass" ]; then
    dbpass="165497381546982"
fi

read -p "Database name [$r_projectname]: " dbname
if [ -z "$dbname" ]; then
    dbname="$r_projectname"
fi

read -p "Postgres data path [/home/$USER/Workspace/.db/postgres/$r_projectname]: " postgrespath
if [ -z "$postgrespath" ]; then
    postgrespath="/home/$USER/Workspace/.db/postgres/$r_projectname"
fi

r_postgrespath=${postgrespath////\\/}

read -p "Redis host [$networkipbase.5]: " redishost
if [[ -z "$redishost" ]]; then
    redishost="$networkipbase.5"
fi

read -p "Redis port [63798] (you must use 5 characters): " redisport
if [ -z "$redisport" ]; then
    redisport="63798"
fi

read -p "Redis data path [/home/$USER/Workspace/.redis/$r_projectname]: " redispath
if [ -z "$redispath" ]; then
    redispath="/home/$USER/Workspace/.redis/$r_projectname"
fi

r_redispath=${redispath////\\/}

r_dockerprojectpath=${DOCKER_PROJECT_PATH////\\/}

printf "\n"

yes | cp -i $DOCKER_PROJECT_PATH/environments/trivago/.env.example $DOCKER_PROJECT_PATH/environments/trivago/.env

echo -e "\n"

sed -i "s/{PROJECT_NAME}/$r_projectname/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{PROJECT_PATH}/$r_projectpath/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{APP_URL}/$appurl/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{NGINX_HOST}/$nginxhost/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{PHP_HOST}/$phphost/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_HOST}/$dbhost/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_USER}/$dbuser/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_PASSWORD}/$dbpass/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_DB}/$dbname/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_PORT}/$dbport/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{POSTGRES_PATH}/$r_postgrespath/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{REDIS_HOST}/$redishost/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{REDIS_PORT}/$redisport/g" $DOCKER_PROJECT_PATH/environments/trivago/.env
sed -i "s/{REDIS_PATH}/$r_redispath/g" $DOCKER_PROJECT_PATH/environments/trivago/.env

yes | cp -i $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml.example $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml

echo -e "\n"

sed -i "s/\${PROJECT_NAME}/$projectname/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${PROJECT_PATH}/$r_projectpath/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${APP_URL}/$appurl/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${NGINX_HOST}/$nginxhost/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${PHP_HOST}/$phphost/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_HOST}/$dbhost/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_USER}/$dbuser/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_PASSWORD}/$dbpass/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_DB}/$dbname/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_PORT}/$dbport/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${POSTGRES_PATH}/$r_postgrespath/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${REDIS_HOST}/$redishost/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${REDIS_PORT}/$redisport/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${REDIS_PATH}/$r_redispath/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${NETWORK_NAME}/$NETWORK_NAME/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${NETWORK_IP}/$NETWORK_IP/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml
sed -i "s/\${DOCKER_PROJECT_PATH}/$r_dockerprojectpath/g" $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml

yes | cp -i $DOCKER_PROJECT_PATH/environments/trivago/nginx/nginx.conf.example $DOCKER_PROJECT_PATH/environments/trivago/nginx/nginx.conf

echo -e "\n"

sed -i "s/{APP_URL}/$appurl/g" $DOCKER_PROJECT_PATH/environments/trivago/nginx/nginx.conf
sed -i "s/{PROJECT_PATH}/$projectname/g" $DOCKER_PROJECT_PATH/environments/trivago/nginx/nginx.conf

yes | cp -i $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/overrides.ini.example $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/overrides.ini

echo -e "\n"

sed -i "s/\${NGINX_HOST}/$nginxhost/g" $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/overrides.ini

yes | cp -i $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/laravel-cron.example $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/laravel-cron

echo -e "\n"

sed -i "s/{PROJECT_NAME}/$r_projectname/g" $DOCKER_PROJECT_PATH/environments/trivago/php-fpm/laravel-cron

read -p "Are you sure you want to install the project [$r_projectname] y/n? [y]: " yn
if [[ "$yn" != "n" ]]; then

    echo -e "\n"

    docker-compose -f $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml --project-name "$r_projectname" up -d --force-recreate --build --remove-orphans

    cp $projectpath/.env.example $projectpath/.env

    docker exec --user docker $r_projectname-php-fpm php -r "file_put_contents('$r_projectname/.env', str_replace([
        'APP_NAME=Laravel',
        'APP_URL=http://localhost',
        'DB_CONNECTION=mysql',
        'DB_HOST=127.0.0.1',
        'DB_PORT=3306',
        'DB_USERNAME=homestead',
        'DB_PASSWORD=secret',
        'DB_DATABASE=homestead'
    ], [
        'APP_NAME=$r_projectname',
        'APP_URL=http://$appurl',
        'DB_CONNECTION=$dbconnection',
        'DB_HOST=$dbhost',
        'DB_PORT=${dbport%?}',
        'DB_USERNAME=$dbuser',
        'DB_PASSWORD=$dbpass',
        'DB_DATABASE=$dbname'
    ], file_get_contents('$r_projectname/.env')));"

    read -p "Would you like to configure smtp (this is required if you want to send emails) y/n? [n]: " yn
    if [ "$yn" = "y" ]; then

        read -p "Encryption type [tls]: " encryptiontype
        if [ -z "$encryptiontype" ]; then
            encryptiontype="tls"
        fi

        read -p "SMTP host [smtp.gmail.com]: " smtphost
        if [ -z "$smtphost" ]; then
            smtphost="smtp.gmail.com"
        fi

        read -p "SMTP port [587]: " smtpport
        if [ -z "$smtpport" ]; then
            smtpport="587"
        fi

        read -p "SMTP user [user@gmail.com]: " smtpuser
        if [ -z "$smtpuser" ]; then
            smtpuser="user@gmail.com"
        fi

        read -p "SMTP password [null]: " smptpass
        if [ -z "$smptpass" ]; then
            smptpass="null"
        fi

        docker exec --user docker $r_projectname-php-fpm php -r "file_put_contents('$r_projectname/.env', str_replace([
            'MAIL_HOST=mailtrap.io',
            'MAIL_PORT=2525',
            'MAIL_USERNAME=null',
            'MAIL_PASSWORD=null',
            'MAIL_ENCRYPTION=null'
        ], [
            'MAIL_HOST=$smtphost',
            'MAIL_PORT=$smtpport',
            'MAIL_USERNAME=$smtpuser',
            'MAIL_PASSWORD=$smptpass',
            'MAIL_ENCRYPTION=$encryptiontype'
        ], file_get_contents('$r_projectname/.env')));"
    fi

    docker exec --user docker $r_projectname-php-fpm /bin/bash -c "cd $r_projectname && composer install"

    docker exec --user docker $r_projectname-php-fpm /bin/bash -c "cd $r_projectname && php artisan key:generate"

    chmod 777 $(find ../storage/ -not -name ".gitignore")
    chmod 777 $(find ../bootstrap/cache/ -not -name ".gitignore")

    docker exec --user docker $r_projectname-php-fpm /bin/bash -c "cd $r_projectname && php artisan migrate:refresh --seed"

    docker exec --user docker $r_projectname-php-fpm /bin/bash -c "cd $r_projectname && php artisan wine-spectator:watch all"

    read -p "Do you want to add the project host on your /etc/hosts file? (warning: This command needs the sudo privilege) y/n? [n]: " yn
    if [[ "$yn" = "y" ]]; then
        etchosts=/etc/hosts
        hostsline="$nginxhost\t$appurl"

        if [ -n "$(grep $appurl $etchosts)" ]
            then
                echo -e "$appurl already exists in $etchosts: $(grep $appurl $etchosts)\n"
            else
                echo -e "Adding $appurl to your $etchosts\n";
                sudo -- sh -c -e "echo '$hostsline' >> $etchosts";

                if [ -n "$(grep $appurl $etchosts)" ]
                    then
                        echo -e "$appurl was added succesfully in $etchosts \n $(grep $appurl $etchosts)";
                    else
                        echo -e "Failed to Add $appurl in $etchosts, Try again!\n";
                fi
        fi
    else
        echo -e "Please, put in your $etchosts file the host of this project: $hostsline\n"
    fi

    echo -e "Project \e[32m$r_projectname\e[0m was successfully installed \n"

    echo -e "Click the link below to access the project: \n"

    echo -e " \e[44mhttp://$appurl\e[0m\n"

else

    echo -e "\n The project was successfully set up \e[32m$r_projectname\e[0m but it was not installed\n"

fi

read -p ''