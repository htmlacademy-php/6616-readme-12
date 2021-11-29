<?php
    require_once 'files.php';

    /**
     * Получает ошибки формы добавления поста
     *
     * @param array $form_data Данные из формы
     * @param array|string $file Файл из формы
     * @param array $forms Массив с формами
     * @param mixed $type_class Тип поста
     *
     * @return array
     */
    function get_post_form_errors(array $form_data, $file, array $forms, $type_class): array
    {
        $errors = [];

        foreach ($form_data as $key => $value) {
            $fields_key = $forms[$type_class]['fields'][$key];

            if (!empty($fields_key['validate_rule'])) {
                $rule = $fields_key['validate_rule'];
                $rule_result = $rule($value);

                if (!empty($rule_result)) {
                    $errors[$key]['name'] = $fields_key['title'];
                    $errors[$key]['description'] = $rule_result;
                }
            }
        }

        if (!empty($file['tmp_name']) && !empty(validate_file($file['tmp_name']))) {
            $errors['post-file-photo']['name'] = 'Фото';
            $errors['post-file-photo']['description'] = validate_file($file['tmp_name']);
        }

        return $errors;
    }

    /**
     * Получает ссылку фотографии, добавленной в посте
     *
     * @param array|string $file Файл из формы
     * @param string|null $link Ссылка из формы
     *
     * @return string Ссылка на загруженный файл
     */
    function get_upload_image_path($file, ?string $link): string
    {
        $image_path = '';

        if (!empty($file['name'])) {
            $image_path = upload_file_from_input($file);
        } elseif ($link) {
            $image_path = upload_file_from_url($link);
        }

        return $image_path;
    }

    /**
     * @param mysqli $connection Подключение к базе данных
     * @param array $tags Хэштеги
     * @param int $post_id ID нового поста
     *
     * @return true;
     */
    function get_post_tags_result(mysqli $connection, array $tags, int $post_id): bool
    {
        foreach ($tags as $tag) {
            $tag_id = null;

            if (!select_tag($connection, [$tag])) {
                insert_tag($connection, [$tag]);
                $tag_id = $connection->insert_id;
            } else {
                $tag_id = select_tag($connection, [$tag])['id'];
            }

            if (!select_tag_from_post($connection, [$tag_id, $post_id])) {
                insert_tag_to_post($connection, [$tag_id, $post_id]);
            }
        }

        return true;
    }
