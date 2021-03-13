# SnowTricks Symfony 5.0.7

[![Maintainability](https://api.codeclimate.com/v1/badges/a744ca7deb74463ec50a/maintainability)](https://codeclimate.com/github/bigboss-oualid/projet_6/maintainability)

## About the project

![Project](https://img.shields.io/badge/Project-6-FF4500.svg)
![Symfony](https://img.shields.io/badge/Symfony-v5.0.7-45CB3E)
[![Repo Size](https://img.shields.io/github/repo-size/bigboss-oualid/projet_6?label=Repo+Size)](https://github.com/bigboss-oualid/projet_6/tree/master)
![Repo Size](https://img.shields.io/w3c-validation/html?preset=HTML%2C%20SVG%201.1%2C%20MathML%203.0%2C%20ITS%202.0&targetUrl=https%3A%2F%2Fsnowtricks.it-bigboss.de)
![request](https://img.shields.io/github/issues-pr-closed/bigboss-oualid/projet_6?color=33FFCC)
![Issues](https://img.shields.io/github/issues-closed/bigboss-oualid/projet_6?logo=logo)

Development of a community website for sharing snowboard tricks using the Symfony framework. The website is developed only with one external bundle, which is **```doctrine/doctrine-fixtures-bundle```** in order to load the data, for the initialization of the project.
##Documentation
* [Getting started](#getting-started)
  * [Prepare your work environment](#prepare-your-work-environment)
    * [Prerequisites](#prerequisites)
    * [Set up the Project](#set-up-the-project)
  * [Try the Application](#try-the-application)
  * [Run Tests](#run-tests)
    * [PHPUnit](#phpunit)
  * [Application Demonstration](#demo)
  * [Information](#info)
#

## Getting Started

### Prepare your work environment

Download & install all prerequisites tools

##### Prerequisites
* [![WampServer](https://img.shields.io/badge/WampServer-v3.2.0-F70094)](https://www.wampserver.com/) OR [![PHP](https://img.shields.io/badge/PHP-%3E%3D7.4.7-7377AD)](https://www.php.net/manual/fr/install.php) + [![MySQL](https://img.shields.io/badge/MySQL-v8.0.19-DF6900)](https://dev.mysql.com/downloads/mysql/#downloads)
* [![Git](https://img.shields.io/badge/Git-v2.27-E94E31)](https://git-scm.com/download)
* [![Symfony CLI](https://img.shields.io/badge/Symfony_CLI-v4.20-000000)](https://symfony.com/download)
* [![Composer](https://img.shields.io/badge/Composer-v1.10.13-5F482F)](https://getcomposer.org/download)
* [![Nodes](https://img.shields.io/badge/Nodejs-v14.5.0-026E00)](https://nodejs.org)

##### Set up the Project
###### Installation
1. Download the project [![Download](https://img.shields.io/badge/-Here-D3D345)](https://codeload.github.com/bigboss-oualid/projet_6/zip/master "click to start download"), or clone the repository by executing the command line from your terminal.the project, or clone the repository. In your terminal from home directory, use the command.
```shell
$ git clone https://github.com/bigboss-oualid/projet_6.git
```

2. In your terminal change the working directory to the project folder and run the below command line to install all dependencies :
```shell 
$ composer install
```


###### Set up Database:
3. Edit the variable ***DATABASE_URL*** at line ``32`` on file **```./.env```** with your database details
 
 > [More info about how you Configure the Database in Symfony](https://symfony.com/doc/current/doctrine.html#configuring-the-database)
 
4. Run ***WampServer*** (Or run Mysql separately, if you don't use Wamp).

5. Create the application database: 
```shell 
$ php bin/console doctrine:database:create
```
6. Create the Database script Tables/Schema:
```shell
$ php bin/console make:migration
```

7. Add tables in the Database:
```shell 
$ php bin/console --no-interaction doctrine:migrations:migrate
```

8. Load the initial data into the application database
```shell 
$ php bin/console doctrine:fixtures:load --group=AppFixtures -n
```

###### Prepare SMTP Server + Web Interface on local machine
> MailDev is a simple way to test your project's generated emails during development with an easy to use web interface
 that runs on your machine built on top of Node.js, which makes it very simple to set up.If you have not already done so, you must first install [Nodejs](https://nodejs.org/fr/) on your machine and thus have the npm command available.

1. Install **[maildev](https://maildev.github.io/maildev/)**
```shell 
$ npm install -g maildev
```
2. Open new ***Terminal*** window & run **maildev**

```shell 
$ maildev
```
3. Navigate to [```http://localhost:1080```](http://localhost:1080) to intercept emails for sign up new users or reset password...

> if you want use other SMTP server, edit the variable ***MAILER_URL*** in line 39 on file ```.env``` in root directory, with your SMTP details. 


### Try the Application:
1. Run Symfony server:
```shell 
# start the server and display the log messages in the console
$ symfony server:start
 
# start the server in the background
$ symfony server:start [-d] 
```
2. Navigate to [localhost:8000](http://localhost:8000) 
> Log in with the account below to modify, create tricks & post comments.

username | email               | password
-------- | ------------------- | --------
demo     | demo@snowtricks.com | demo  
 
### Run Tests
Before testing the fileUploader service, check that the image `./test/Service/test/test.png` exists if not Copy an 
image to the same folder and rename it `test.png`.
```shell
# Run all tests of the app
$ ./bin/phpunit

# Run tests for one class (replace CLASS_NAME with the name of class you want test)
$ ./bin/phpunit --filter CLASS_NAME
```

Replace ``CLASS_NAME`` in the command line with :
`BackendControllerTest`, `FrontendControllerTest`, `SecurityControllerTest`, `TrickTest`, `FileUploaderSubscriberTest` or `UserRepositoryTest`

### Demo
**Visit the application demonstration:**  [![SnowTricks](https://img.shields.io/badge/-SnowTricks-FF4500.svg)](https://snowtricks.it-bigboss.de/ "Jimmy Sweat")

### Info 
###### Author
[**Walid BigBoss**](https://it-bigboss.de)

###### Copyright

Code released under the MIT License.
[![GitHub License](https://img.shields.io/github/license/bigboss-oualid/projet_6.svg?label=License)](https://github.com/bigboss-oualid/projet_6/blob/master/LICENSE)
