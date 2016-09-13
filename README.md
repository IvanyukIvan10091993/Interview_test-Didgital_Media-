# Interview_test(Didgital_Media)
1. Для вывода в консоль результатов работы первого задания нужно запустить init.php в task1
```shell
php task1/init.php
```
1. Для проверки второго задания:
  1. Создать подключение к БД:
    1. Залогиниться в mysql
      ```shell
      mysql -u имяПользоателяБд -p
      ```
    1. Создать базу данных командами:
      ```shell
      DROP DATABASE IF NOT EXISTS ivanivanyuk_task2;
      CREATE DATABASE IF NOT EXISTS ivanivanyuk_task2;
      ```
    1. Выйти из mysql
    1. Вставить свои значения в task2/config/db.php
    1. Заполнить БД командой
      ```shell
      php task2/yii migrate
      ```
