<?php
    /**
     * Получает список типов постов
     *
     * @param mysqli $connection Подключение к базе данных
     *
     * @return array Возвращается массив с типами постов
     */
    function get_content_types(mysqli $connection): array
    {
        $sql = 'SELECT * FROM content_type';

        return sql_select_all($connection, $sql);
    }
