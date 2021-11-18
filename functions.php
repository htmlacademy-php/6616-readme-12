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

        $days = intval($dateDiff->format('%a'));
        $hours = intval($dateDiff->format('%H'));
        $minutes = intval($dateDiff->format('%i'));

        $dateText = !$isRegisterDate ? 'назад' : 'на сайте';

        if ($days >= 35) {
            $months = floor($days / 30);
            $monthsDeclination = get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев');

            return "{$months} {$monthsDeclination} {$dateText}";
        }

        if ($days >= 7 && $days < 35) {
            $weeks = floor($days / 7);
            $weeksDeclination = get_noun_plural_form($weeks, 'неделя', 'недели', 'недель');

            return "{$weeks} {$weeksDeclination} {$dateText}";
        }

        if ($days > 0 && $days < 7) {
            $daysDeclination = get_noun_plural_form($days, 'день', 'дня', 'дней');

            return "{$days} {$daysDeclination} {$dateText}";
        }

        if ($hours > 0 && $hours < 24) {
            $hoursDeclination = get_noun_plural_form($hours, 'час', 'часа', 'часов');

            return "{$hours} {$hoursDeclination} {$dateText}";
        }

        $minutesDeclination = get_noun_plural_form($minutes, 'минута', 'минуты', 'минут');

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

        if (!$dbConnection) {
            print('Ошибка подключения: ' . mysqli_connect_error());
            exit;
        }

        mysqli_set_charset($dbConnection, 'utf8');

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
        $sql = 'SELECT p.*, u.login, u.avatar, ct.type_class,
                COALESCE(pl.cnt, 0) AS likes_count, COALESCE(pc.cnt, 0) AS comments_count
                FROM post p
                INNER JOIN user u ON p.user_id = u.id
                INNER JOIN content_type ct ON p.content_type_id = ct.id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_like GROUP BY post_id) pl ON p.id = pl.post_id
                LEFT OUTER JOIN (SELECT post_id, COUNT(*) AS cnt FROM post_comment GROUP BY post_id) pc ON p.id = pc.post_id';
        if ($filterTypeId) {
            $sql .= ' WHERE ct.id = ' . $filterTypeId;
        }
        $sql .= ' ORDER BY show_count DESC';

        $sqlQuery = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC);
    }

    /**
     * Получает пост из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     * @param int $postId ID поста из параметра
     *
     * @return array Возвращается массив с постом
     */
    function getPost(mysqli $dbConnection, int $postId): array
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
                WHERE p.id = ' . $postId;

        $sqlQuery = mysqli_query($dbConnection, $sql);
        $sqlResult = mysqli_fetch_assoc($sqlQuery);

        if ($sqlResult !== null) {
            return $sqlResult;
        } else {
            return [];
        }
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
     * Получает список всех хэштегов из базы данных
     *
     * @param mysqli $dbConnection Подключение к базе данных
     *
     * @return array Возвращается массив с хэштегами
     */
    function getTags(mysqli $dbConnection): array
    {
        $sql = 'SELECT id, name FROM hashtag';

        $sqlQuery = mysqli_query($dbConnection, $sql);

        return mysqli_fetch_all($sqlQuery, MYSQLI_ASSOC);
    }

    /**
     * Определяет, нужен ли класс активности для фильтра или нет
     *
     * @param int $filterTypeId
     * @param int $typeId
     *
     * @return string Возвращается класс для активности или пустую строку
     */
    function activePopularFilterHandler(int $filterTypeId, int $typeId = 0): string
    {
        if ($filterTypeId === $typeId) {
            return ' filters__button--active';
        } else {
            return '';
        }
    }

    /**
     * Определяет, нужен ли класс активности для фильтра или нет
     *
     * @param int $tabTypeId
     * @param int $typeId
     *
     * @return string Возвращается класс для активности или пустую строку
     */
    function activeAddFilterHandler(int $tabTypeId, int $typeId = 1): string
    {
        if ($tabTypeId === $typeId) {
            return ' filters__button--active tabs__item--active';
        } else {
            return '';
        }
    }

    /**
     * Получает тип файла
     *
     * @param $filename
     *
     * @return string
     */
    function getFileType($filename): string
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($fileInfo, $filename);
    }

    /**
     * Получает расширение файла
     *
     * @param $fileType
     *
     * @return string
     */
    function getFileExt($fileType): string
    {
        $typeArray = explode('/', $fileType);

        return '.' . array_pop($typeArray);
    }

    /**
     * Сохраняет файл из интернета
     *
     * @param $fileUrl
     *
     * @return string
     */
    function saveFileFromLink($fileUrl): string
    {
        $file = file_get_contents($fileUrl);
        $filename = uniqid();
        $path = 'uploads/' . $filename;
        file_put_contents($path, $file);

        return $path;
    }

    /**
     * Добавляет фотографию к посту
     *
     * @param $file
     * @param $link
     *
     * @return string
     */
    function getUploadImagePath($file, $link): string
    {
        $path = '';

        if (!empty($file['name'])) {
            $fileType = getFileType($file['tmp_name']);
            $filename = uniqid() . getFileExt($fileType);
            $path = 'uploads/' . $filename;
            move_uploaded_file($file['tmp_name'], $path);
        } elseif ($link) {
            $tmpPath = saveFileFromLink($link);
            $fileType = getFileType($tmpPath);
            $fileExt = getFileExt($fileType);
            $path = $tmpPath . $fileExt;
            rename($tmpPath, $path);
        }

        return $path;
    }

    /**
     * Добавляем теги в базу и привязывает их к посту
     *
     * @param $tags
     * @param $postId
     * @param $connection
     *
     * @return bool
     */
    function insertTags($tags, $postId, $connection): bool
    {
        if (count($tags) !== 0) {
            $tagsIds = [];
            $oldTags = getTags($connection);

            foreach ($oldTags as $oldTag) {
                if (in_array($oldTag['name'], $tags)) {
                    $key = array_search($oldTag['name'], $tags);
                    $tagsIds[] = intval($oldTag['id']);
                    unset($tags[$key]);
                }
            }

            if (count($tags) !== 0) {
                $insertTags = [];
                $addedTags = [];

                for ($i = 0; $i < count($tags); $i++) {
                    $insertTags[] = '(?)';
                    $addedTags[] = '?';
                }

                $insertTagsImplode = implode(',', $insertTags);
                $addedTagsImplode = implode(',', $addedTags);

                $insertTagsSql = 'INSERT INTO hashtag (name) VALUES ' . $insertTagsImplode;
                mysqli_stmt_execute(db_get_prepare_stmt($connection, $insertTagsSql, $tags));

                $addedTagsSql = 'SELECT id, name FROM hashtag WHERE name IN (' . $addedTagsImplode . ')';
                $stmt = db_get_prepare_stmt($connection, $addedTagsSql, $tags);
                mysqli_stmt_execute($stmt);
                $addedTagsResult = mysqli_stmt_get_result($stmt);
                $addedTagsList = mysqli_fetch_all($addedTagsResult, MYSQLI_ASSOC);

                foreach ($addedTagsList as $addedTag) {
                    $tagsIds[] = $addedTag['id'];
                }
            }

            $insertTagsPosts = [];
            $postTagsIds = [];

            foreach ($tagsIds as $tagId) {
                $insertTagsPosts[] = '(? , ?)';
                $postTagsIds[] = $postId;
                $postTagsIds[] = $tagId;
            }

            $sqlTagsPostsCon = 'INSERT INTO post_hashtag (post_id, hashtag_id) VALUES ' . implode(',', $insertTagsPosts);

            return mysqli_stmt_execute(db_get_prepare_stmt($connection, $sqlTagsPostsCon, $postTagsIds));
        }

        return false;
    }
