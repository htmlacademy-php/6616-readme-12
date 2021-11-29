<?php
    /**
     * Получает список комментариев определенного поста из базы данных
     *
     * @param mysqli $connection Подключение к базе данных
     * @param int $post_id ID поста
     *
     * @return array Возвращается массив с комментариями
     */
    function get_post_comments(mysqli $connection, int $post_id): array
    {
        $sql = 'SELECT pc.id, pc.date_add, pc.content, u.login, u.avatar
                FROM post_comment pc
                INNER JOIN user u ON pc.user_id = u.id
                INNER JOIN post p ON pc.post_id = p.id
                WHERE post_id = ' . $post_id;

        return sql_select_all($connection, $sql);
    }
