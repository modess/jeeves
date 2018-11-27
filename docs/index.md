---
title: Introduction
---

Settings up continuous integration with Jenkins for your PHP application have been a quite daunting task. It used to consist a lot of XML configuration files and manual configuration in Jenkins web GUI. The other option was going with a hosted service that managed the CI process for you, but since it's a resource intensive process it's always expensive.

This project aims to simplify that task and make it available to everyone, disregarding budget. It consists of two parts, a Composer package for generating the files you need in your repository, and a script for installing Jenkins and everything needed on the server.

What follows is the documentation from using the Composer package in your application to generate the necessary files Jenkins needs, and then using the installer script on the server/instance that is going to host the Jenkins service. After that there's still some configuration to be done in the web GUI, but this aims to be a step-by-step guide to completing it.

## Requirements

* Your application needs to support PHP7 (`7.0`, `7.1` or `7.2`)
* Your application is hosted on Github
* The server/instance needs to run Linux Ubuntu `14.04`, `16.04` or `18.04`
* The server/instance should have at least 1GB of RAM

## Features

* Run unit tests (PHPUnit)
* Fast PHP linting in parallel (PHP parallel lint)
* Code style violations (PHP Code sniffer)
* Code complexity (PHP mess detector)
* Duplicate code detection (PHP copy/paste detector)
* Slack notifications, optional