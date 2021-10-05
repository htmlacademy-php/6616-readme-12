<?php
    /**
     * Урезает оригинальный текст, если его длина меньше заданного числа символов
     *
     * @param string $content Оригинальный текст
     * @param int $lengthLimit Лимит на число символов
     *
     * @return string Возвращается либо урезанный текст с ссылкой, либо оригинальный
     */
    function trimContent(string $content, int $lengthLimit = 300): string
    {
        $moreLink = '<a class="post-text__more-link" href="#">Читать далее</a>';
        $contentWords = explode(' ', $content);
        $contentLength = 0;
        $trimmedWords = [];

        foreach ($contentWords as $word) {
            $contentLength += mb_strlen($word) + 1;

            if ($contentLength <= $lengthLimit) {
                $trimmedWords[] = $word;
            } else {
                break;
            }
        }

        $trimmedContent = implode(' ', $trimmedWords);

        return $contentLength > $lengthLimit ? "<p>{$trimmedContent}...</p>{$moreLink}" : "<p>{$content}</p>";
    }

    /**
     * Отображает дату в относительный формате
     *
     * @param string $date Оригинальная дата размещения поста
     *
     * @return string Возвращается относительный формат даты, исходя из оригинальной
     */
    function getRelativeData(string $date, bool $isRegisterDate = false): string
    {
        $dateCurrent = date_create();
        $dateCreate = date_create($date);
        $dateDiff = date_diff($dateCurrent, $dateCreate);

        $days = intval($dateDiff->format("%a"));
        $hours = intval($dateDiff->format("%H"));
        $minutes = intval($dateDiff->format("%i"));

        $dateText = !$isRegisterDate ? 'назад' : 'на сайте';

        if ($days >= 35) {
            $months = floor($days / 30);
            $monthsDeclination = get_noun_plural_form($months, "месяц", "месяца", "месяцев");

            return "{$months} {$monthsDeclination} {$dateText}";
        }

        if ($days >= 7 && $days < 35) {
            $weeks = floor($days / 7);
            $weeksDeclination = get_noun_plural_form($weeks, "неделя", "недели", "недель");

            return "{$weeks} {$weeksDeclination} {$dateText}";
        }

        if ($days > 0 && $days < 7) {
            $daysDeclination = get_noun_plural_form($days, "день", "дня", "дней");

            return "{$days} {$daysDeclination} {$dateText}";
        }

        if ($hours > 0 && $hours < 24) {
            $hoursDeclination = get_noun_plural_form($hours, "час", "часа", "часов");

            return "{$hours} {$hoursDeclination} {$dateText}";
        }

        $minutesDeclination = get_noun_plural_form($minutes, "минута", "минуты", "минут");

        return "{$minutes} {$minutesDeclination} {$dateText}";
    }

    /**
     * Подключает к базе данный
     *
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     *
     * @return mysqli Возвращается результат подключения
     */
    function getConnection(string $hostname, string $username, string $password, string $database): mysqli
    {
        $dbConnection = mysqli_connect($hostname, $username, $password, $database);

        if ( !$dbConnection) {
            print("Ошибка подключения: " . mysqli_connect_error());
            exit;
        }

        mysqli_set_charset($dbConnection, "utf8");

        return $dbConnection;
    }

    /**
     * Получает список типов постов
     *
     * @param mysqli $dbConnection Подключение к базе данных
     *
     * @return array Возвращается массив с типами постов
     */
    function getContentTypes(mysqli $dbConnection): array
    {
        $sql = 'SELECT * FROM content_type';
        $sqlQuery = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC);
    }

    /**
     * Получает список постов из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $filterTypeId ID типа постов из параметра
     *
     * @return array Возвращается массив с постами
     */
    function getPosts(mysqli $dbConnection, int $filterTypeId): array
    {
        $sql = 'SELECT p.*, u.login, u.avatar, ct.type_class
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id';
        if ($filterTypeId) {
            $sql .= ' WHERE ct.id = ' . $filterTypeId;
        }
        $sql .= ' ORDER BY show_count DESC';

        $sqlQuery = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC);
    }

    /**
     * Получает список комментариев определенного поста из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $postId ID поста
     *
     * @return array Возвращается массив с комментариями
     */
    function getPostComments(mysqli $dbConnection, int $postId): array
    {
        $sql = 'SELECT pc.id, pc.date_add, pc.content, u.login, u.avatar
                FROM post_comment pc
                INNER JOIN user u ON pc.user_id = u.id
                INNER JOIN post p ON pc.post_id = p.id
                WHERE post_id = ' . $postId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC);
    }

    /**
     * Получает пост из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $postId ID поста из параметра
     *
     * @return array|false Возвращаемся массив с постом или отрицание, если такого поста нет
     */
    function getPost(mysqli $dbConnection, int $postId)
    {
        $sql = 'SELECT p.*, u.login, u.avatar, u.date_add as register_date, ct.type_class
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id
                WHERE p.id = ' . $postId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        if ( !$sqlQuery) {
            return false;
        } else {
            return mysqli_fetch_assoc($sqlQuery);
        }
    }

    /**
     * Получает количество лайков для поста
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $postId ID поста из параметра
     *
     * @return int Возвращаемся количество лайков
     */
    function getPostLikesCount(mysqli $dbConnection, int $postId)
    {
        $sql = 'SELECT COUNT(*) as count FROM post_like WHERE post_id = ' . $postId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        if ( !$sqlQuery) {
            return false;
        } else {
            $sqlResult = mysqli_fetch_assoc($sqlQuery);

            return (int)$sqlResult[ 'count' ];
        }
    }

    /**
     * Получает количество комментариев для поста
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $postId ID поста из параметра
     *
     * @return int Возвращаемся количество комментариев
     */
    function getPostCommentsCount(mysqli $dbConnection, int $postId)
    {
        $sql = 'SELECT COUNT(*) as count FROM post_comment WHERE post_id = ' . $postId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        if ( !$sqlQuery) {
            return false;
        } else {
            $sqlResult = mysqli_fetch_assoc($sqlQuery);

            return (int)$sqlResult[ 'count' ];
        }
    }

    /**
     * Получает количество подписчиков пользователя
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $userId ID пользователя
     *
     * @return int Возвращаемся подписчиков постов
     */
    function getUserSubscribersCount(mysqli $dbConnection, int $userId)
    {
        $sql = 'SELECT COUNT(*) as count FROM user_subscription WHERE user_id = ' . $userId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        if ( !$sqlQuery) {
            return false;
        } else {
            $sqlResult = mysqli_fetch_assoc($sqlQuery);

            return (int)$sqlResult[ 'count' ];
        }
    }

    /**
     * Получает количество постов пользователя
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $userId ID пользователя
     *
     * @return int Возвращаемся количество постов
     */
    function getUserPostsCount(mysqli $dbConnection, int $userId)
    {
        $sql = 'SELECT COUNT(*) as count FROM post WHERE user_id = ' . $userId;

        $sqlQuery = mysqli_query($dbConnection, $sql);

        if ( !$sqlQuery) {
            return false;
        } else {
            $sqlResult = mysqli_fetch_assoc($sqlQuery);

            return (int)$sqlResult[ 'count' ];
        }
    }

    /**
     * Определяет, нужен ли класс активности для фильтра или нет
     *
     * @param int $filterTypeId
     * @param int $typeId
     *
     * @return string Возвращаем класс для активности или пустую строку
     */
    function activeFilterHandler(int $filterTypeId, int $typeId = 0): string
    {
        if ($filterTypeId === $typeId) {
            return ' filters__button--active';
        } else {
            return '';
        }
    }

