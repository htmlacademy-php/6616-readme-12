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
    function getRelativeData(string $date): string
    {
        $dateCurrent = date_create();
        $dateCreate = date_create($date);
        $dateDiff = date_diff($dateCurrent, $dateCreate);

        $days = intval($dateDiff->format("%a"));
        $hours = intval($dateDiff->format("%H"));
        $minutes = intval($dateDiff->format("%i"));

        if ($hours > 0 && $hours < 24) {
            $hoursDeclination = get_noun_plural_form($hours, "час", "часа", "часов");

            return "{$hours} {$hoursDeclination} назад";
        }

        if ($days > 0 && $days < 7) {
            $daysDeclination = get_noun_plural_form($days, "день", "дня", "дней");

            return "{$days} {$daysDeclination} назад";
        }

        if ($days >= 7 && $days < 35) {
            $weeks = floor($days / 7);
            $weeksDeclination = get_noun_plural_form($weeks, "неделя", "недели", "недель");

            return "{$weeks} {$weeksDeclination} назад";
        }

        if ($days >= 35) {
            $months = floor($days / 30);
            $monthsDeclination = get_noun_plural_form($months, "месяц", "месяца", "месяцев");

            return "{$months} {$monthsDeclination} назад";
        }

        $minutesDeclination = get_noun_plural_form($minutes, "минута", "минуты", "минут");

        return "{$minutes} {$minutesDeclination} назад";
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
        $sqlResult = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);
    }

    /**
     * Получает список постов из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     *
     * @return array Возвращается массив с постами
     */
    function getPosts(mysqli $dbConnection): array
    {
        $sql = 'SELECT p.*, u.login, u.avatar, ct.type_class
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id
                ORDER BY show_count DESC';
        $sqlResult = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);
    }
