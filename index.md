---
layout: default
---

# Introduction

Settings up continuous integration with Jenkins for your PHP application has in the past been a quite daunting task. It used to consist a lot of XML configuration files and manual configuration in Jenkins web GUI. This project aims to simplify that task. It consists of two parts, a Composer package for generating the files you need in your repository, and a script for installing Jenkins and everything needed on the server.

## Prerequisites

* Your application needs to support PHP7 (`7.0`, `7.1` or `7.2`).
* The server/instance you're installing Jenkins on needs to run Linux Ubuntu `14.04` or `16.04` (but might work with other versions).
* The configuration guide will assume your project is hosted on Github, but it'll work with other providers also.

## Features

* Run unit tests (PHPUnit)
* Fast linting in parallel (PHP parallel lint)
* Code style violations (PHP Code sniffer)
* Code complexity (PHP mess detector)
* Duplicate code (PHP copy/paste detector)
* Slack notifications, optional

This will be done for all branches and pull requests.

# Installation

## Package

Add it to your dependencies with

```
composer require --dev modess/jeeves
```

Now run it to generate the files for your project.

```
./vendor/bin/jeeves generate
```

Commit the files to your repository and push them to your remote.

### PHPUnit

You need to manually add a configuration file for PHPUnit with code coverage. How that looks depends on your application, here are some examples:

* [Generic PHP application](https://gist.github.com/modess/bbdea9e94f04c672d57c67e0ac371c01)
* [Laravel application](https://gist.github.com/modess/5bcec07bb894e0a20c4ffb85f663cdda)

Save it as `phpunit.xml.dist` in your project. If you change the `build` directory when generating the files for your project, update the file accordingly. 

### Additional packages

If you want more control over the tools and versions of them you're running, add them to your projects' dependencies. Otherwise they'll be installed globally on your Jenkins server.

* `phpunit/phpunit`: Run unit tests and generate code coverage
* `squizlabs/php_codesniffer`: Checking that code styles follows a standard
* `phpmd/phpmd`: Check code complexity
* `sebastian/phpcpd`: Check for code duplication
* `jakub-onderka/php-parallel-lint`: Fast PHP linting in parallel