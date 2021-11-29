<?php
    /**
     * Проверяет поле на заполнение
     *
     * @param string $value Значение поля
     *
     * @return string Результат в виде сообщения
     */
    function validate_filled(string $value): string
    {
        return !$value ? 'Поле не заполнено' : '';
    }

    /**
     * Проверяет теги на корректное написания
     *
     * @param string $value Значение поля
     *
     * @return string Результат в виде сообщения
     */
    function validate_tags(string $value): string
    {
        $message = '';
        $tags = explode(' ', $value);

        if (!$value) {
            $message = '';
        } else {
            foreach ($tags as $value) {
                if (!preg_match('/^\w+$/ui', $value)) {
                    $message = 'Теги должны быть разделены пробелами и могут состоять из букв, цифр и символа подчеркивания';
                    break;
                }
            }
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление ссылки
     *
     * @param string $value Значение поля
     *
     * @return string Результат в виде сообщения
     */
    function validate_url(string $value): string
    {
        return !filter_var($value, FILTER_VALIDATE_URL) ? 'Указан некорректный URL-адрес' : '';
    }

    /**
     * Проверяет корректное добавление ссылки на видео
     *
     * @param string $value Значение поля
     *
     * @return string Результат в виде сообщения
     */
    function validate_video_url(string $value): string
    {
        $message = validate_url($value);

        if (!$message) {
            $result = check_youtube_url($value);

            if (gettype($result) === 'string') {
                $message = $result;
            }
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление пути картинки
     *
     * @param string $value Значение поля
     *
     * @return string Результат в виде сообщения
     */
    function validate_photo_url(string $value): string
    {
        $file_types = ['image/png', 'image/jpeg', 'image/gif'];

        if (!empty($_FILES['post-file-photo']['name'])) {
            $message = '';
        } elseif (!$value) {
            $message = 'Укажите ссылку из интернета или загрузите файл';
        } else {
            $message = validate_url($value);

            if (!$message) {
                if (!file_get_contents($value)) {
                    $message = 'Не удалось загрузить файл';
                } elseif (!in_array(get_headers($value, 1)['Content-Type'], $file_types)) {
                    $message = 'Некорректный тип файла';
                }
            }
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление файла
     *
     * @param string $file_path Путь к файлу
     *
     * @return string Результат в виде сообщения
     */
    function validate_file(string $file_path): string
    {
        $message = '';
        $file_types = ['image/png', 'image/jpeg', 'image/gif'];
        $file_type = get_file_type($file_path);

        if (!in_array($file_type, $file_types)) {
            $message = 'Некорректный тип файла';
        }

        return $message;
    }
