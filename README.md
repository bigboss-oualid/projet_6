# SnowTricks Symfony 5.0.7

[![Maintainability](https://api.codeclimate.com/v1/badges/01c142989cee5fd53e9a/maintainability)](https://codeclimate.com/github/bigboss-oualid/projet_6/maintainability)

## About the project

![Project](https://img.shields.io/badge/Project-6-blue.svg)
![Symfony](https://img.shields.io/badge/Symfony-v5.0.7-Lime.svg)

Development of a community website for sharing snowboard tricks using the Symfony framework. The website is developed only with one external bundle, which is **```doctrine/doctrine-fixtures-bundle```** in order to load the data, for the initialization of the project.

## Prerequisites

* ![WampServer](https://img.shields.io/badge/WampServer-v3.2.0-DeepPink.svg) OR ![PHP](https://img.shields.io/badge/PHP-v7.4.7-SlateBlue.svg) + ![MySQL](https://img.shields.io/badge/MySQL-v8.0.19-Goldenrod.svg)
* ![Git](https://img.shields.io/badge/Git-v2.26.2-OrangeRed.svg)
* ![Symfony](https://img.shields.io/badge/Symfony-v5.0.7-Lime.svg)
* ![Composer](https://img.shields.io/badge/Composer-v1.10.6-SaddleBrown.svg)
* ![Nodejs](https://img.shields.io/badge/Nodejs-v14.4.0-DarkGreen.svg)
* ![Maildev](https://img.shields.io/badge/Maildev-v1.1.0-DeepSkyBlue.svg)
## Getting Started
#### Prepare your work environment
Download & install :
- [WampServer](https://www.wampserver.com/) OR separatly [PHP](https://www.php.net/manual/fr/install.php) & [MySql](https://dev.mysql.com/downloads/mysql/#downloads)
- [Git](https://git-scm.com/download)
- [Symfony CLI](https://symfony.com/download)
- [Composer](https://getcomposer.org/download/)
- [Nodejs](https://nodejs.org/fr/)

#### Install the Project
1. [Download](https://codeload.github.com/bigboss-oualid/projet_6/zip/master) the project, or clone the repository. In your terminal from home directory, use the command.
> [![Repo Size](https://img.shields.io/github/repo-size/bigboss-oualid/projet_6?label=Repo+Size)](https://github.com/bigboss-oualid/projet_6/tree/master)
```shell
$ git clone https://github.com/bigboss-oualid/projet_6.git
```
2. In your terminal change the working directory to the project folder and run the below command lines.
3. Install dependencies :
```shell 
$ composer install
```
4. Edit the variable ***DATABASE_URL*** in line 32 on file ```.env``` in root with your database details (skip this step if you installed wamp with root). [more info](https://symfony.com/doc/current/doctrine.html#configuring-the-database)
5. Run ***WampServer*** (run Mysql if you don't use Wamp and you have modified ***DATABASE_URL*** in step 4 ).
6. Create the application database with command: 
```shell 
$ php bin/console doctrine:database:create
```
7. Create entity tables
```shell
$ php bin/console make:migration
```
8. Add tables to Database 
```shell 
$ php bin/console --no-interaction doctrine:migrations:migrate
```
9. Load the initial data into the application database
```shell 
$ php bin/console doctrine:fixtures:load --group=AppFixtures -n
```
10. Run PHP's built-in Web Server 
```shell 
$ symfony server:start
```
11. Navigate to [localhost:8000](http://localhost:8000) 

#### Test your project's generated emails on local
* MailDev is a simple way to test your project's generated emails during development with an easy to use web interface that runs on your machine built on top of Node.js, which makes it very simple to set up.If you have not already done so, you must first install [Nodejs](https://nodejs.org/fr/) on your machine and thus have the npm command available.

1. Install **maildev**
```shell 
$ npm install -g maildev
```
2. Open new ***Terminal*** window & run **maildev** 
```shell 
$ maildev
```
3. Navigate to [```http://localhost:1080```](http://localhost:1080) to intercept emails for sign up new users or reset password...

> if you want use other SMTP server, edit the variable ***MAILER_URL*** in line 39 on file ```.env``` in root directory, with your SMTP details. 

#### Users accounts
username | email | password
---- | ----- | --------
demo| demo@snowtricks.com | demo  
 
#### Run Tests
before testing the fileUploader service, check that the image `./test/Service/test/test.png` exists if not Copy an image to the same folder and rename it `test.png`

Open your terminal:

* Run all tests 
```shell
$ ./bin/phpunit
```
* Run only one class test
```shell
$ ./bin/phpunit --filter ClassNameTest
```
Replace ``ClassNameTest`` in the command line with :
`BackendControllerTest`, `FrontendControllerTest`, `SecurityControllerTest`, `TrickTest`, `FileUploaderSubscriberTest` or `UserRepositoryTest`

 
###### Author
[**Walid BigBoss**](https://it-bigboss.de)

###### Copyright

Code released under the MIT License.

[![GitHub License](https://img.shields.io/github/license/bigboss-oualid/projet_6.svg?label=License)](https://github.com/bigboss-oualid/projet_6/blob/master/LICENSE.md)

 