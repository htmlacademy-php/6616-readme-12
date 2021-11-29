<?php
    /**
     * Получает список постов из базы данных
     *
     * @param mysqli $connection Подключение к базе данных
     * @param int $filter_type_id ID типа постов из параметра
     *
     * @return array Возвращается массив с постами
     */
    function get_posts(mysqli $connection, int $filter_type_id): array
    {
        $sql = 'SELECT p.*, u.login, u.avatar, ct.type_class,
                COALESCE(pl.cnt, 0) AS likes_count, COALESCE(pc.cnt, 0) AS comments_count
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_like GROUP BY post_id) pl ON p.id = pl.post_id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_comment GROUP BY post_id) pc ON p.id = pc.post_id';

        if ($filter_type_id) {
            $sql .= ' WHERE ct.id = ' . $filter_type_id;
        }

        $sql .= ' ORDER BY show_count DESC';

        return sql_select_all($connection, $sql);
    }

    /**
     * Получает пост из базы данных
     *
     * @param mysqli $connection Подключение к базе данных
     * @param int $post_id ID поста из параметра
     *
     * @return array Возвращает массив с постом
     */
    function get_post(mysqli $connection, int $post_id): array
    {
        $sql = 'SELECT p.*,  u.login, u.avatar, u.date_add as date_register, ct.type_class,
                COALESCE(pl.cnt, 0) AS likes_count, COALESCE(pc.cnt, 0) AS comments_count, COALESCE(us.cnt, 0) AS subscribers_count, COALESCE(up.cnt, 0) AS posts_count
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_like GROUP BY post_id) pl ON p.id = pl.post_id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_comment GROUP BY post_id) pc ON p.id = pc.post_id
                LEFT OUTER JOIN (SELECT user_id, COUNT(*) AS cnt FROM user_subscription GROUP BY user_id) us ON p.user_id = us.user_id
                LEFT OUTER JOIN (SELECT user_id, COUNT(*) AS cnt FROM post GROUP BY user_id) up ON p.user_id = up.user_id
                WHERE p.id = ' . $post_id;

        return sql_select_single($connection, $sql);
    }

    /**
     * Добавляем пост в базу данных
     *
     * @param mysqli $connection Подключение к базе данных
     * @param string $title Заголовок поста
     * @param string|null $text Контент поста
     * @param string|null $author Автор цитаты
     * @param string|null $image Ссылка на изображение
     * @param string|null $video Ссылка на видео
     * @param string|null $link Ссылка из поста
     * @param int $user_id ID пользователя
     * @param int $type_id ID типа поста
     *
     * @return mysqli_stmt
     */
    function add_post(
        mysqli $connection,
        string $title,
        ?string $text,
        ?string $author,
        ?string $image,
        ?string $video,
        ?string $link,
        int $user_id,
        int $type_id
    ): mysqli_stmt {
        $sql = 'INSERT INTO post (title, content, quote_author, image, video, link, user_id, content_type_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);';

        return prepared_query($connection, $sql, [$title, $text, $author, $image, $video, $link, $user_id, $type_id]);
    }
