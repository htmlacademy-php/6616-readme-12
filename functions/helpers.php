<?php
    /**
     * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
     *
     * Примеры использования:
     * is_date_valid('2019-01-01'); // true
     * is_date_valid('2016-02-29'); // true
     * is_date_valid('2019-04-31'); // false
     * is_date_valid('10.10.2010'); // false
     * is_date_valid('10/10/2010'); // false
     *
     * @param string $date Дата в виде строки
     *
     * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
     */
    function is_date_valid(string $date): bool
    {
        $format_to_check = 'Y-m-d';
        $date_time_object = date_create_from_format($format_to_check, $date);

        return $date_time_object !== false && array_sum(date_get_last_errors()) === 0;
    }

    /**
     * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
     *
     * @param mysqli $link Ресурс соединения
     * @param string $sql SQL запрос с плейсхолдерами вместо значений
     * @param array $data Данные для вставки на место плейсхолдеров
     *
     * @return mysqli_stmt Подготовленное выражение
     */
    function db_get_prepare_stmt(mysqli $link, string $sql, array $data = []): mysqli_stmt
    {
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt === false) {
            $error_message = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
            die($error_message);
        }

        if ($data) {
            $types = '';
            $stmt_data = [];

            foreach ($data as $value) {
                $type = 's';

                if (is_int($value)) {
                    $type = 'i';
                } else {
                    if (is_string($value)) {
                        $type = 's';
                    } else {
                        if (is_double($value)) {
                            $type = 'd';
                        }
                    }
                }

                $types .= $type;
                $stmt_data[] = $value;
            }

            $values = array_merge([$stmt, $types], $stmt_data);

            $func = 'mysqli_stmt_bind_param';
            $func(...$values);

            if (mysqli_errno($link) > 0) {
                $error_message = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
                die($error_message);
            }
        }

        return $stmt;
    }

    /**
     * Возвращает корректную форму множественного числа
     * Ограничения: только для целых чисел
     *
     * Пример использования:
     * $remaining_minutes = 5;
     * echo 'Я поставил таймер на {$remaining_minutes} ' .
     *     get_noun_plural_form(
     *         $remaining_minutes,
     *         'минута',
     *         'минуты',
     *         'минут'
     *     );
     * Результат: 'Я поставил таймер на 5 минут'
     *
     * @param int $number Число, по которому вычисляем форму множественного числа
     * @param string $one Форма единственного числа: яблоко, час, минута
     * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
     * @param string $many Форма множественного числа для остальных чисел
     *
     * @return string Рассчитанная форма множественного числа
     */
    function get_noun_plural_form(int $number, string $one, string $two, string $many): string
    {
        $number = (int)$number;
        $mod10 = $number % 10;

        switch (true) {
            case ($mod10 === 1):
                return $one;
            case ($mod10 >= 2 && $mod10 <= 4):
                return $two;
            default:
                return $many;
        }
    }

    /**
     * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
     *
     * @param string $name Путь к файлу шаблона относительно папки templates
     * @param array $data Ассоциативный массив с данными для шаблона
     *
     * @return string Итоговый HTML
     */
    function include_template(string $name, array $data = []): string
    {
        $name = 'view/templates/' . $name;
        $result = '';

        if (!is_readable($name)) {
            return $result;
        }

        ob_start();
        extract($data);
        require $name;

        return ob_get_clean();
    }

    /**
     * Функция проверяет доступно ли видео по ссылке на youtube
     *
     * @param string $url Ссылка на видео
     *
     * @return string Ошибку если валидация не прошла
     */
    function check_youtube_url(string $url)
    {
        $id = extract_youtube_id($url);

        set_error_handler(function () { }, E_WARNING);
        $headers = get_headers('https://www.youtube.com/oembed?format=json&url=https://www.youtube.com/watch?v=' . $id);
        restore_error_handler();

        if (!is_array($headers)) {
            return 'Видео по такой ссылке не найдено. Проверьте ссылку на видео';
        }

        $err_flag = strpos($headers[0], '200') ? 200 : 404;

        if ($err_flag !== 200) {
            return 'Видео по такой ссылке не найдено. Проверьте ссылку на видео';
        }

        return true;
    }

    /**
     * Возвращает код iframe для вставки youtube видео на страницу
     *
     * @param string $youtube_url Ссылка на youtube видео
     *
     * @return string
     */
    function embed_youtube_video(string $youtube_url): string
    {
        $res = '';
        $id = extract_youtube_id($youtube_url);

        if ($id) {
            $src = 'https://www.youtube.com/embed/' . $id;
            $res = '<iframe width="760" height="400" src="' . $src . '" frameborder="0"></iframe>';
        }

        return $res;
    }

    /**
     * Возвращает img-тег с обложкой видео для вставки на страницу
     *
     * @param string $youtube_url Ссылка на youtube видео
     *
     * @return string
     */
    function embed_youtube_cover(string $youtube_url): string
    {
        $res = '';
        $id = extract_youtube_id($youtube_url);

        if ($id) {
            $src = sprintf('https://img.youtube.com/vi/%s/mqdefault.jpg', $id);
            $res = '<img alt="youtube cover" width="360" height="200" src="' . $src . '" />';
        }

        return $res;
    }

    /**
     * Извлекает из ссылки на youtube видео его уникальный ID
     *
     * @param string $youtube_url Ссылка на youtube видео
     *
     * @return array
     */
    function extract_youtube_id(string $youtube_url)
    {
        $id = false;

        $parts = parse_url($youtube_url);

        if ($parts) {
            if ($parts['path'] == '/watch') {
                parse_str($parts['query'], $vars);
                $id = $vars['v'] ?? null;
            } else {
                if ($parts['host'] == 'youtu.be') {
                    $id = substr($parts['path'], 1);
                }
            }
        }

        return $id;
    }

    /**
     * @param $index
     *
     * @return false|string
     */
    function generate_random_date($index)
    {
        $deltas = [['minutes' => 59], ['hours' => 23], ['days' => 6], ['weeks' => 4], ['months' => 11]];
        $dcnt = count($deltas);

        if ($index < 0) {
            $index = 0;
        }

        if ($index >= $dcnt) {
            $index = $dcnt - 1;
        }

        $delta = $deltas[$index];
        $time_val = rand(1, current($delta));
        $time_name = key($delta);

        $ts = strtotime('$time_val $time_name ago');

        return date('Y-m-d H:i:s', $ts);
    }

    /**
     * Урезает оригинальный текст, если его длина меньше заданного числа символов
     *
     * @param string $content Оригинальный текст
     * @param int $length_limit Лимит на число символов
     *
     * @return string Возвращается либо урезанный текст с ссылкой, либо оригинальный
     */
    function trim_content(string $content, int $length_limit = 300): string
    {
        $more_link = '<a class="post-text__more-link" href="#">Читать далее</a>';
        $content_words = explode(' ', $content);
        $content_length = 0;
        $trimmed_words = [];

        foreach ($content_words as $word) {
            $content_length += mb_strlen($word) + 1;

            if ($content_length <= $length_limit) {
                $trimmed_words[] = $word;
            } else {
                break;
            }
        }

        $trimmed_content = implode(' ', $trimmed_words);

        return $content_length > $length_limit ? "<p>{$trimmed_content}...</p>{$more_link}" : "<p>{$content}</p>";
    }

    /**
     * Отображает дату в относительный формате
     *
     * @param string $date Оригинальная дата размещения поста
     *
     * @return string Возвращается относительный формат даты, исходя из оригинальной
     */
    function get_relative_data(string $date, bool $is_register_date = false): string
    {
        $date_current = date_create();
        $date_create = date_create($date);
        $date_diff = date_diff($date_current, $date_create);

        $days = intval($date_diff->format('%a'));
        $hours = intval($date_diff->format('%H'));
        $minutes = intval($date_diff->format('%i'));

        $date_text = !$is_register_date ? 'назад' : 'на сайте';

        if ($days >= 35) {
            $months = floor($days / 30);
            $months_declination = get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев');

            return "{$months} {$months_declination} {$date_text}";
        }

        if ($days >= 7 && $days < 35) {
            $weeks = floor($days / 7);
            $weeks_declination = get_noun_plural_form($weeks, 'неделя', 'недели', 'недель');

            return "{$weeks} {$weeks_declination} {$date_text}";
        }

        if ($days > 0 && $days < 7) {
            $days_declination = get_noun_plural_form($days, 'день', 'дня', 'дней');

            return "{$days} {$days_declination} {$date_text}";
        }

        if ($hours > 0 && $hours < 24) {
            $hours_declination = get_noun_plural_form($hours, 'час', 'часа', 'часов');

            return "{$hours} {$hours_declination} {$date_text}";
        }

        $minutes_declination = get_noun_plural_form($minutes, 'минута', 'минуты', 'минут');

        return "{$minutes} {$minutes_declination} {$date_text}";
    }

    /**
     * Получает значение POST запроса формы
     *
     * @param string $name Название параметра
     *
     * @return string Значение параметра
     */
    function get_field_value(string $name): string
    {
        return $_POST[$name] ?? '';
    }

    /**
     * Получает тип файла
     *
     * @param string $file_name Файл
     *
     * @return string Значение типа файла
     */
    function get_file_type(string $file_name): string
    {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($file_info, $file_name);
    }

    /**
     * Получает расширение файла
     *
     * @param string $file_type Тип файла
     *
     * @return string Значение расширения файла
     */
    function get_file_ext(string $file_type): string
    {
        $type_array = explode('/', $file_type);

        return '.' . array_pop($type_array);
    }

    /**
     * Подготавливаемый запрос
     *
     * @param mysqli $connection Подключение к базе данных
     * @param string $sql Запрос
     * @param array $params Параметры
     *
     * @return mysqli_stmt
     */
    function prepared_query(mysqli $connection, string $sql, array $params): mysqli_stmt
    {
        $types = str_repeat('s', count($params));
        $stmt = $connection->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Запрос на получение
     *
     * @param mysqli $connection Подключение к базе данных
     * @param string $sql Запрос
     * @param array|null $params Параметры
     *
     * @return mysqli_result
     */
    function sql_select(mysqli $connection, string $sql, array $params = null): mysqli_result
    {
        if (!$params) {
            return $connection->query($sql);
        }

        return prepared_query($connection, $sql, $params)->get_result();
    }

    /**
     * Запрос на получение одного элемента
     *
     * @param mysqli $connection Подключение к базе данных
     * @param string $sql Запрос
     * @param array|null $params Параметры
     *
     * @return array|null Элемент из базы
     */
    function sql_select_single(mysqli $connection, string $sql, array $params = null): ?array
    {
        return sql_select($connection, $sql, $params)->fetch_assoc();
    }

    /**
     * Запрос на получение всех элементов
     *
     * @param mysqli $connection Подключение к базе данных
     * @param string $sql Запрос
     * @param array|null $params Параметры
     *
     * @return array|null Элемент из базы
     */
    function sql_select_all(mysqli $connection, string $sql, array $params = null): ?array
    {
        return sql_select($connection, $sql, $params)->fetch_all(MYSQLI_ASSOC);
    }
