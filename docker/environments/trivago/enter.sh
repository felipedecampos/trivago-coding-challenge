#!/usr/bin/env bash

projectpath="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
globalpath="$(dirname "$(dirname "$projectpath")")"

if ! source $globalpath/.env; then
    clear
    echo -e "The global .env file not found to uninstall the project"
fi

if ! source $projectpath/.env; then
    clear
    echo -e "The project .env file not found to uninstall the project"
fi

handleContainers()
{
    while read -p " Type the container number you want to enter: " inputService
    do
        printf "\n"

        handleExitProgram ${inputService}

        if [[ -z ${inputService} ]]; then
            continue

        elif [[ ${runningContainers[$inputService]} = "$PROJECT_NAME-webserver" ]]; then
            sudo docker exec -it $PROJECT_NAME-webserver sh
            break

        elif [[ ${runningContainers[$inputService]} = "$PROJECT_NAME-php-fpm" ]]; then
            sudo docker exec -it $PROJECT_NAME-php-fpm /bin/bash -c "cd $PROJECT_NAME && /bin/bash"
            break

        elif [[ ${runningContainers[$inputService]} = "$PROJECT_NAME-redis" ]]; then
            sudo docker exec -it $PROJECT_NAME-redis sh
            break

        elif [[ ${runningContainers[$inputService]} = "$PROJECT_NAME-rabbitmq" ]]; then
            sudo docker exec -it $PROJECT_NAME-rabbitmq sh
            break

        elif [[ ${runningContainers[$inputService]} = "$PROJECT_NAME-postgres" ]]; then
            echo -e " Type the password to postgres user: \e[32m$POSTGRES_USER\e[0m and database: \e[32m$POSTGRES_DB\e[0m\n"
            sudo docker exec -it $PROJECT_NAME-postgres psql $POSTGRES_DB $POSTGRES_USER
            break

        else
            printf "\n Container number not found. \n\n";
        fi
    done

    main
}

handleExitProgram()
{
    if [[ -z $1 ]]; then
        return 1

    elif [[ $1 = "0" ]]; then
        exit 0

    fi
}

showHeader()
{
    clear

    header="\0
        # \n
        # Running containers: \n
        # \n
    "

    echo -e ${header}
}

showInstructions()
{
    declare -ga containers=( $(sudo docker ps | awk '{if(NR>1) print $NF}' | grep -e "^$PROJECT_NAME-*") )

    if [[ ${#containers[@]} == 0 ]]; then
        clear

        noEnvironmentsRunning="\n
            There isn't any container running for this project: \e[32m$PROJECT_NAME\e[0m \n
        "

        echo -e ${noEnvironmentsRunning}

        read -p ''

        exit 0
    fi

    showHeader

    instructions="\0
        # \n
        # Which container do you want to enter: \n
        # \n
    "

    declare -ga runningContainers

    runningContainers[0]="Sair"

    cod=0

    for key in ${!containers[@]}; do
        cod=${cod}+1

        runningContainers[cod]=${containers[$key]}
    done

    for key in ${!runningContainers[@]}; do
        echo -e " $key- ${runningContainers[$key]}\n"
    done

    echo -e ${instructions}
}

main()
{
    showInstructions

    handleContainers
}

main