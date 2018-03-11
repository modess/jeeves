#!/bin/bash
# Thanks to https://github.com/coto/server-easy-install for these function
lowercase(){
    echo "$1" | sed "y/ABCDEFGHIJKLMNOPQRSTUVWXYZ/abcdefghijklmnopqrstuvwxyz/"
}
shootProfile(){
    OS=`lowercase \`uname\``
    KERNEL=`uname -r`
    MACH=`uname -m`

    if [ "${OS}" == "windowsnt" ]; then
        OS=windows
    elif [ "${OS}" == "darwin" ]; then
        OS=mac
    else
        OS=`uname`
        if [ "${OS}" = "SunOS" ] ; then
            OS=Solaris
            ARCH=`uname -p`
            OSSTR="${OS} ${REV}(${ARCH} `uname -v`)"
        elif [ "${OS}" = "AIX" ] ; then
            OSSTR="${OS} `oslevel` (`oslevel -r`)"
        elif [ "${OS}" = "Linux" ] ; then
            if [ -f /etc/redhat-release ] ; then
                DistroBasedOn='RedHat'
                DIST=`cat /etc/redhat-release |sed s/\ release.*//`
                PSUEDONAME=`cat /etc/redhat-release | sed s/.*\(// | sed s/\)//`
                REV=`cat /etc/redhat-release | sed s/.*release\ // | sed s/\ .*//`
            elif [ -f /etc/SuSE-release ] ; then
                DistroBasedOn='SuSe'
                PSUEDONAME=`cat /etc/SuSE-release | tr "\n" ' '| sed s/VERSION.*//`
                REV=`cat /etc/SuSE-release | tr "\n" ' ' | sed s/.*=\ //`
            elif [ -f /etc/mandrake-release ] ; then
                DistroBasedOn='Mandrake'
                PSUEDONAME=`cat /etc/mandrake-release | sed s/.*\(// | sed s/\)//`
                REV=`cat /etc/mandrake-release | sed s/.*release\ // | sed s/\ .*//`
            elif [ -f /etc/debian_version ] ; then
                DistroBasedOn='Debian'
                if [ -f /etc/lsb-release ] ; then
                        DIST=`cat /etc/lsb-release | grep '^DISTRIB_ID' | awk -F=  '{ print $2 }'`
                            PSUEDONAME=`cat /etc/lsb-release | grep '^DISTRIB_CODENAME' | awk -F=  '{ print $2 }'`
                            REV=`cat /etc/lsb-release | grep '^DISTRIB_RELEASE' | awk -F=  '{ print $2 }'`
                        fi
            fi
            if [ -f /etc/UnitedLinux-release ] ; then
                DIST="${DIST}[`cat /etc/UnitedLinux-release | tr "\n" ' ' | sed s/VERSION.*//`]"
            fi
            OS=`lowercase $OS`
            DistroBasedOn=`lowercase $DistroBasedOn`
            readonly OS
            readonly DIST
            readonly DistroBasedOn
        fi
    fi
}

# Check OS support
shootProfile

if [ "${DIST}" != "Ubuntu" ] ; then
    echo "ERROR: This script only works for Ubuntu at the moment, exiting"
    exit 1
fi

# PHP version
read -p "Which PHP version to install? [7.0 / 7.1 / 7.2] " PHP_VERSION
if ! [[ $PHP_VERSION =~ ^(7.0|7.1|7.2) ]] || [[ -z $PHP_VERSION ]]; then
    echo "ERROR: Incorrect PHP version, exiting"
    exit 1
fi

# Add Jenkins and PHP repositories
sudo apt-get update
sudo apt-get install -y software-properties-common
wget -q -O - https://pkg.jenkins.io/debian/jenkins-ci.org.key | sudo apt-key add -
sudo sh -c 'echo deb http://pkg.jenkins.io/debian-stable binary/ > /etc/apt/sources.list.d/jenkins.list'
sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php

# Install Jenkins, PHP and general packages
sudo apt-get update
sudo apt-get install -y \
    git-core \
    curl \
    unzip \
    jenkins \
    php${PHP_VERSION} \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-xdebug \
    php${PHP_VERSION}-xsl \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-mbstring

# Install Composer and packages
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
sudo chown -R ${USER}:${USER} ~/.composer/
sudo su - jenkins -c "composer global config minimum-stability dev"
sudo su - jenkins -c "composer global config prefer-stable true"
sudo su - jenkins -c "composer global require \
    phpunit/phpunit \
    squizlabs/php_codesniffer \
    phpmd/phpmd \
    sebastian/phpcpd"

# Get initial admin password
INITIAL_ADMIN_PASSWORD=`sudo cat /var/lib/jenkins/secrets/initialAdminPassword`

# Download cli tool
sudo su - jenkins -c "wget http://localhost:8080/jnlpJars/jenkins-cli.jar -P /var/lib/jenkins"

# Install Jenkins plugins
sudo su - jenkins -c "java -jar jenkins-cli.jar -s http://localhost:8080 install-plugin \
    pipeline-model-definition \
    pipeline-stage-view \
    slack \
    checkstyle \
    cloverphp \
    crap4j \
    dry \
    htmlpublisher \
    pmd \
    violations \
    warnings \
    xunit \
    git \
    greenballs \
    --username admin --password ${INITIAL_ADMIN_PASSWORD}"

# Safe restart Jenkins
sudo su - jenkins -c "java -jar jenkins-cli.jar -s http://localhost:8080 safe-restart --username admin --password ${INITIAL_ADMIN_PASSWORD}"

# Install complete
EXTERNAL_IP=`dig +short myip.opendns.com @resolver1.opendns.com`
echo
echo "##########################################"
echo "# INSTALLATION COMPLETE                  #"
echo "##########################################"
echo "Jenkins should be available at http://${EXTERNAL_IP}:8080"
echo "Login with admin/${INITIAL_ADMIN_PASSWORD}"
exit 0
