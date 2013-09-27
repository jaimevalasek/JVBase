JVBase 1.0
================
Create By: Jaime Marcelo Valasek

Module base to use structure AbstractMapper and AbstractService to ZF2

Start program with a structure ready. Advisable to use the Module JVBase for beginners and / or people who have no knowledge of how to build a well structured project.

Installation
-----
Download this module into the vendor folder of your project.

Enable the module in the file application.config.php. Add the module JVBase.

create the service connection to the database with the name Zend\Db\Adapter\Adapter

### With composer

1. Edit composer.json

`"require": {
    "jaimemarcelo/jv-base": "dev-master"
}`

2. Now tell composer to download ZfcUser by running the command:

`php composer.phar update`

### Post installation

`<?php
return array(
    'modules' => array(
        // ...
        'JVBase',
    ),
    // ...
);`

Usage tutorials
-----
http://www.youtube.com/zf2tutoriais

http://www.zf2.com.br/tutoriais