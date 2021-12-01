<?php
    require_once 'helpers.php';

    if (!file_exists('config.php')) {
        print('Отсутствует файл config.php');
        exit;
    }

    $config = require 'config.php';

    /**
     * Подключает к базе данный
     *
     * @param string $hostname Название хоста
     * @param string $username Название пользователя
     * @param string $password Пароль пользователя
     * @param string $database Название базы
     *
     * @return mysqli Возвращается результат подключения
     */
    function get_connection(string $hostname, string $username, string $password, string $database): mysqli
    {
        $db_connection = mysqli_connect($hostname, $username, $password, $database);

        if (!$db_connection) {
            print('Ошибка подключения: ' . mysqli_connect_error());
            exit;
        }

        mysqli_set_charset($db_connection, 'utf8');

        return $db_connection;
    }

    $connection = get_connection($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['name']);
