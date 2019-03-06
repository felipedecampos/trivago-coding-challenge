#!/usr/bin/env bash

setUpDockerPath()
{
    clear

    old_networkname="{NETWORK_NAME}"
    old_networkip="{NETWORK_IP}"
    dockerpath="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    r_dockerpath=${dockerpath////\\/}

    if source ./.env; then
        old_networkname=$NETWORK_NAME
        old_networkip=$NETWORK_IP
    fi

    yes | cp -i ./.env.example ./.env

    if [[ $1 = "without-network" ]]; then
        old_networkname="{NETWORK_NAME}"
        old_networkip="{NETWORK_IP}"
    fi

    echo -e "\n"

    sed -i "s/{NETWORK_NAME}/$old_networkname/g" .env
    sed -i "s/{NETWORK_IP}/$old_networkip/g" .env
    sed -i "s/{DOCKER_PROJECT_PATH}/$r_dockerpath/g" .env
}

handleExitProgram()
{
    if [[ -z $1 ]]; then
        return 1

    elif [[ $1 = "quit" ]]; then
        exit 0

    elif [[ $1 = "q" ]]; then
        exit 0

    fi
}

handleService()
{
    while read -p " Type the service number you want to use: " inputService
    do
        printf "\n"

        handleExitProgram ${inputService}

        if [[ -z ${inputService} ]]; then
            continue

        elif [[ ${inputService} == 1 ]]; then
            showNetwork
            break

        elif [[ ${inputService} == 2 ]]; then
            setUpNetwork
            break

        elif [[ ${inputService} == 3 ]]; then
            installTrivago
            break

        elif [[ ${inputService} == 4 ]]; then
            enterTrivago
            break

        elif [[ ${inputService} == 5 ]]; then
            uninstallTrivago
            break

        else
            printf "\n Service number not found. \n\n";
        fi
    done

    main
}

showHeader()
{
    clear

    header="\0
        # \n
        # Environments with Docker \n
        # \n
        # Type: 'q', 'quit' or 'Ctrl + C' to exit the program \n
        # \n\n
        Services: \n
    "

    echo -e ${header}
}

showInstructions()
{
    showHeader

    declare -ga services=(
        [1]="Show current Network"
        [2]="Set up new Network"
        [3]="Install project: \e[32mTrivago\e[0m"
        [4]="Enter into container: \e[32mTrivago\e[0m"
        [5]="Uninstall project: \e[32mTrivago\e[0m"
    )

    instructions="\0
        # \n
        # Which service do you want to use: \n
        # \n
    "

    for key in ${!services[@]}; do

        echo -e " $key- ${services[$key]}\n"

    done

    echo -e ${instructions}
}

main()
{
    showInstructions

    handleService
}

showNetwork()
{
    clear

    if source ./.env; then
        if [[ -n $(sudo docker network ls --filter name="$NETWORK_NAME" --quiet) ]]; then
            sudo docker network inspect $(sudo docker network ls --filter name="$NETWORK_NAME" --quiet)
        elif [[ -n "$NETWORK_NAME" && "$NETWORK_NAME" != "{NETWORK_NAME}" ]]; then
            echo -e "Network set up: \e[32m$NETWORK_NAME\e[0m > \e[32m$NETWORK_IP\e[0m";
        else
            echo -e "There isn't any Network set up"
        fi
    else
        clear
        echo -e "There isn't any Network set up"
    fi

    read -p ''
}

setUpNetwork()
{
    clear

    while read -p "Type the network name [example: trivago]: " networkname
    do
        printf "\n"

        if [[ -z ${networkname} ]]; then
            continue

        elif [[ ${networkname} = "q" ]]; then
            return 0

        elif [[ ${networkname} = "quit" ]]; then
            return 0

        else
            break

        fi
    done

    showUsedIps

    while read -p "Type an IP for the network: " networkip
    do
        networkiphead=$(cut -d '.' -f 1 <<<"${networkip}")
        networkiptail=$(cut -d '.' -f 2,3,4 <<<"${networkip}")

        printf "\n"

        if [[ -z ${networkip} ]]; then
            continue

        elif [[ ${networkip} = "q" ]]; then
            return 0

        elif [[ ${networkip} = "quit" ]]; then
            return 0

        elif ! inArray foundBaseIpNetworks "$networkiphead"; then
            echo -e "There is already a network created with this start IP: \033[0;31m$networkiphead\033[0m.$networkiptail \n"
            continue

        else
            break

        fi
    done

    setUpDockerPath without-network

    echo -e "\n"

    sed -i "s/{NETWORK_NAME}/$networkname/g" .env
    sed -i "s/{NETWORK_IP}/$networkip/g" .env

    clear
    printf "\n"
    echo -e " Network \e[32m${networkname}\e[0m successfully set up \n"

    read -p ''
}

inArray () {
    local name=$1[@]
    local array=("${!name}")
    local seeking=$2
    local rtn=0 # true

    for element in "${array[@]}"; do
        if [[ $element -eq $seeking ]]; then
            rtn=1 # false
            break
        fi
    done

    return $rtn
}

showUsedIps()
{
    networks=($(sudo docker network ls | grep -v -e "host" -e "none" | awk '{ if(NR>1) print $2 }'))

    old="$IFS"
    IFS="|"
    str="${networks[*]}"
    findNetworks="$str"
    IFS=$old

    declare -ga foundBaseIpNetworks

    foundNetworks=($(sudo docker network inspect --format="{{ range .IPAM.Config }}{{ .Subnet }}{{end}}" $(sudo docker network ls --filter name="${findNetworks}" -q) | sed -e 's/\/.*//g'))
    foundBaseIpNetworks=($(sudo docker network inspect --format="{{ range .IPAM.Config }}{{ .Subnet }}{{end}}" $(sudo docker network ls --filter name="${findNetworks}" -q) | sed -e 's/\..*//g'))

    if [[ "${#foundNetworks[@]}" != "0" ]]; then
        printf "Don't use the following IPs, they are already in use:\n"

        for i in ${!foundNetworks[@]}; do
            printf "> ${foundNetworks[$i]}\n"
        done

        printf "\n"
    fi
}

installTrivago()
{
    clear

    bash ${DOCKER_PROJECT_PATH}/environments/trivago/install.sh
}

enterTrivago()
{
    clear

    bash ${DOCKER_PROJECT_PATH}/environments/trivago/enter.sh
}

uninstallTrivago()
{
    bash ${DOCKER_PROJECT_PATH}/environments/trivago/uninstall.sh
}

setUpDockerPath

main
