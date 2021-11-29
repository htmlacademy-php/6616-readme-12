<?php
    /**
     * Получает хэштег из базы
     *
     * @param mysqli $connection Подключение к базе данных
     * @param array $hashtag Хэштег
     *
     * @return array|null Хэштег из базы
     */
    function select_tag(mysqli $connection, array $hashtag): ?array
    {
        $sql = 'SELECT id, name FROM hashtag WHERE name = ?';

        return sql_select_single($connection, $sql, $hashtag);
    }

    /**
     * Получает хэштег из базы, привязанный к посту
     *
     * @param mysqli $connection Подключение к базе данных
     * @param array $data Хэштег и id поста
     *
     * @return array|null Хэштег и id поста из базы
     */
    function select_tag_from_post(mysqli $connection, array $data): ?array
    {
        $sql = 'SELECT hashtag_id, post_id FROM post_hashtag WHERE hashtag_id = ? && post_id = ?';

        return sql_select_single($connection, $sql, $data);
    }

    /**
     * Добавляет хэштег в базу
     *
     * @param mysqli $connection Подключение к базе данных
     * @param array $hashtag Хэштег
     *
     * @return mysqli_stmt
     */
    function insert_tag(mysqli $connection, array $hashtag): mysqli_stmt
    {
        $sql = 'INSERT INTO hashtag (name) VALUES (?)';

        return prepared_query($connection, $sql, $hashtag);
    }

    /**
     * Добавляет связь хэштега и поста
     *
     * @param mysqli $connection Подключение к базе данных
     * @param array $data Хэштег и id поста
     *
     * @return mysqli_stmt
     */
    function insert_tag_to_post(mysqli $connection, array $data): mysqli_stmt
    {
        $sql = 'INSERT INTO post_hashtag (hashtag_id, post_id) VALUES (?, ?)';

        return prepared_query($connection, $sql, $data);
    }
