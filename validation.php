<?php
    require_once('helpers.php');
    require_once('functions.php');

    /**
     * Проверяет поле на заполнение
     *
     * @param $value
     *
     * @return string
     */
    function validateFilled($value): string
    {
        $message = '';

        if (!$value) {
            $message = 'Поле не заполнено';
        }

        return $message;
    }

    /**
     * Проверяет теги на корректное написания
     *
     * @param $value
     *
     * @return string
     */
    function validateTags($value): string
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
     * Проверяет корректное добавление файла
     *
     * @param $filePath
     *
     * @return string
     */
    function validateFile($filePath): string
    {
        $message = '';
        $fileTypes = ['image/png', 'image/jpeg', 'image/gif'];
        $fileType = getFileType($filePath);

        if (!in_array($fileType, $fileTypes)) {
            $message = 'Некорректный тип файла';
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление ссылки
     *
     * @param $value
     *
     * @return string
     */
    function validateUrl($value): string
    {
        $message = '';

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $message = 'Указан некорректный URL-адрес';
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление пути картинки
     *
     * @param $value
     *
     * @return string
     */
    function validatePhotoUrl($value): string
    {
        $fileTypes = ['image/png', 'image/jpeg', 'image/gif'];

        if (!empty($_FILES['file']['name'])) {
            $message = '';
        } elseif (!$value) {
            $message = 'Укажите ссылку из интернета или загрузите файл';
        } else {
            $message = validateUrl($value);

            if (!$message) {
                if (!file_get_contents($value)) {
                    $message = 'Не удалось загрузить файл';
                } elseif (!in_array(get_headers($value, 1)['Content-Type'], $fileTypes)) {
                    $message = 'Некорректный тип файла';
                }
            }
        }

        return $message;
    }

    /**
     * Проверяет корректное добавление ссылки на видео
     *
     * @param $value
     *
     * @return string
     */
    function validateVideoUrl($value): string
    {
        $message = validateUrl($value);

        if (!$message) {
            $result = check_youtube_url($value);

            if (gettype($result) === 'string') {
                $message = $result;
            }
        }

        return $message;
    }
