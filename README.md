# Interview_test(Didgital_Media)
1. Для вывода в консоль результатов работы первого задания нужно запустить init.php в task1
```shell
php task1/init.php
```
1. Для проверки второго задания:
  1. Перейти в task2
    ```shell
    cd task2
    ```
  1. Подтянуть зависимости
    ```shell
    composer install
    ```
  1. Создать подключение к БД:
    1. Залогиниться в mysql
      ```shell
      mysql -u имяПользоателяБд -p
      ```
    1. Создать базу данных
      ```shell
      DROP DATABASE IF EXISTS ivanivanyuk_task2;
      CREATE DATABASE IF NOT EXISTS ivanivanyuk_task2;
      ```
    1. Выйти из mysql
    1. Вставить свои значения в config/db.php
    1. Заполнить БД
      ```shell
      php yii migrate
      ```
    1. Запустить yii сервер
      ```shell
      php yii serve
      ```
    1. Открыть в браузере http://localhost:8080/index.php
    1. Перейти по ссылке и создать тестового пользователя (введите простую почту вроде 123@123.ru)
    1. Открыть монитор производительности браузера и попробовать создать пользователя с такой же почтой
    1. Монитор производительности покажет, что страница не перезагружалась, а сообщение об ошибке подтянуто с помощью ajax
    1. Залогинить в mysql и удалить тестовую БД
    ```shell
    DROP DATABASE IF EXISTS ivanivanyuk_task2;
    ```
