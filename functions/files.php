<?php
    /**
     * Сохраняет файл по ссылке из интернета
     *
     * @param string $url Ссылка
     *
     * @return string Путь к файлу
     */
    function upload_file_from_url(string $url): string
    {
        $file = file_get_contents($url);
        $file_name = uniqid();
        $tmp_path = 'uploads/' . $file_name;
        file_put_contents($tmp_path, $file);
        $file_type = get_file_type($tmp_path);
        $file_ext = get_file_ext($file_type);
        $file_path = $tmp_path . $file_ext;
        rename($tmp_path, $file_path);

        return $file_path;
    }

    /**
     * Сохраняет файл из поля для загрузки
     *
     * @param array|string $file Файл
     *
     * @return string Путь к файлу
     */
    function upload_file_from_input($file): string
    {
        $file_type = get_file_type($file['tmp_name']);
        $file_name = uniqid() . get_file_ext($file_type);
        $file_path = 'uploads/' . $file_name;
        move_uploaded_file($file['tmp_name'], $file_path);

        return $file_path;
    }
