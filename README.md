Yii 2 Content
=========

Yii 2 Content - Контент модуль реализующий добавление статей на сайт

## Features

* Easy install
* Work with seo tags
* Work with images
* Tree

## Installation

Используя композер

php composer.phar require --prefer-dist stepancher/yii2-content-module "*"

* Запустить миграции
        php yii migrate --migrationPath=@vendor/stepancher/yii2-content-module/migrations

*  Подключить в common/config/main.php

'modules' => [
          'content' => [
              'class' => 'stepancher\content\Content',
              'imageDir' => "@app/../upload/content", // Image for upload files
              'imageUrl' => "/upload/content" // Url to images
          ],
      ],



* Создать роуты во фронтенде, что то вроде по первому url будет список всех статей, по второму отдельная статья
    'articles'=>'content/default/list',
    'articles/<url>'=>'content/default/show',
* Создать роуты в бэкенде
    'content' =>'content/admin',
    'content/<slug>'=>'content/admin/<slug>',
* Раздать права пользователю или роли на просмотр и редактирование статей

 