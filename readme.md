Szükséglet
1. Másold át .env.example-t .env-be
   cp .env.example .env

2. Futtasd a Composer telepítést
   composer install

3. Indítsd el a Docker konténereket
   docker-compose up -d

Alkalmazás

1. Lépj be a PHP konténerbe
   docker exec -it php_container bash

2. Futtasd a PHPUnit teszteket, az összes teszt futtatása

   ./vendor/bin/phpunit --testdox
2. 
   Vagy futtasd az Integration/DatabaseTest.php tesztet

   ./vendor/bin/phpunit --testdox tests/Integration/DatabaseTest.php

3. Futtasd az alkalmazást

   php public/index.php