---
title: Setup application
---

## Install Jeeves and generate configuration

Add it to your dependencies with

```
composer require --dev modess/jeeves
```

Now run it to generate the files for your project.

```
./vendor/bin/jeeves generate
```

## Install additional PHP packages

If you want more control over the tools and versions of them you're running, add them to your projects' dependencies. Otherwise they'll be installed globally on your Jenkins server.

* `phpunit/phpunit`: Run unit tests and generate code coverage
* `squizlabs/php_codesniffer`: Checking that code styles follows a standard
* `phpmd/phpmd`: Check code complexity
* `sebastian/phpcpd`: Check for code duplication
* `jakub-onderka/php-parallel-lint`: Fast PHP linting in parallel


## PHPUnit configuration file

You need to manually add a configuration file for PHPUnit with code coverage. How that looks depends on your application, here are some examples:

* [Generic PHP application](https://gist.github.com/modess/bbdea9e94f04c672d57c67e0ac371c01)
* [Laravel application](https://gist.github.com/modess/5bcec07bb894e0a20c4ffb85f663cdda)

Save it as `phpunit.xml.dist` in your project and add `phpunit.xml` to your `.gitignore` file so you can override it. If you change the `build` directory when generating the files for your project, update the file accordingly. 

Commit the files to your repository and push them to your remote.
