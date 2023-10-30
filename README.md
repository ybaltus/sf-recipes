# Introduction

Création d'une application pour gérer des recettes avec Symfony 6.

## Stack technique
* Symfony v6.3
* PHP 8
* Doctrine & Mariadb
* symfony/webpack-encore-bundle
* bootswatch
* doctrine/doctrine-fixtures-bundle
* knplabs/knp-paginator-bundle
* easycorp/easyadmin-bundle
* karser/karser-recaptcha3-bundle
* vich/uploader-bundle
* fakerphp/faker

## Pour lancer le projet avec Symfony CLI

```
1. composer install
2. symfony console doctrine:database:create
3. symfony console doctrine:migrations:migrate
4. symfony console doctrine:fixture:load
5. npm install / yarn install
6. npm run dev / yarn dev
7. symfony server:start
```

## Pour lancer le projet avec docker

```
1. docker-compose up -d
2. Go to localhost:8000
```
