#!/usr/bin/env bash

projectpath="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
globalpath="$(dirname "$(dirname "$projectpath")")"

if ! source $globalpath/.env; then
    clear
    echo -e "The global .env file not found to uninstall the project \n"
    read -p ''
    exit 1
fi

if ! source $projectpath/.env; then
    clear
    echo -e "The .env file not found to uninstall the project \n"
    read -p ''
    exit 1
fi

echo -e "\e[33m"

read -p " Are you sure do you want to uninstall the project $PROJECT_NAME (notice: There is no way to undo this) y/n? [n]: " yn
if [[ "$yn" = "y" ]]; then
    echo -e "\e[0m"

    # DESTROYING CONTAINERS, VOLUMES, IMAGES, NETWORKS
    sudo docker-compose -f $DOCKER_PROJECT_PATH/environments/trivago/docker-compose.yml down --volumes --rmi all --remove-orphans

    echo -e "\n"

    # REMOVING HOSTS
    etchosts=/etc/hosts

    if [[ -n "$(grep "$APP_URL" $etchosts)" ]]; then
        if [[ -n $(sudo sed "/$APP_URL/d" /etc/hosts) ]]; then
            echo "$APP_URL was removed successfully from $etchosts";
        else
            echo "Failed to remove $APP_URL from $etchosts, Try again!";
        fi
    else
        echo "Host ($APP_URL) has already been removed from $etchosts: $(grep $APP_URL $etchosts)"
    fi

    echo -e "\n"

    read -p "Do you want to remove the project repository (notice: There is no way to undo this) y/n? [n]: " yn
    if [[ "$yn" = "y" ]]; then
        sudo rm -rf $PROJECT_PATH
    fi

    echo -e "\n"

    echo -e "\e[32mProject $PROJECT_NAME was successfully uninstalled\e[0m"

    read -p ''
fi

echo -e "\e[0m \n"