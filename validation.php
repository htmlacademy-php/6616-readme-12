<?php
    require_once('helpers.php');

    /**
     * @param $title
     *
     * @return string
     */
    function validateTitle($title): string
    {
        if (empty($_POST[ $title ])) {
            return 'Поле не заполнено';
        }

        return '';
    }

    /**
     * @param $text
     *
     * @return string
     */
    function validateText($text): string
    {
        if (empty($_POST[ $text ])) {
            return 'Поле не заполнено';
        }

        return '';
    }

    /**
     * @param $author
     *
     * @return string
     */
    function validateAuthor($author): string
    {
        if (empty($_POST[ $author ])) {
            return 'Поле не заполнено';
        }

        return '';
    }

    /**
     * @param $url
     *
     * @return string
     */
    function validateUrl($url): string
    {
        $urlValue = $_POST[ $url ];

        if (isset($_FILES[ 'userpic-file-photo' ])) {
            $fileTypes = ['image/png', 'image/jpeg', 'image/gif'];
            if ($_FILES[ 'userpic-file-photo' ][ 'error' ] && empty($urlValue)) {
                return 'Укажите ссылку или загрузите фотографию';
            } elseif ( !$_FILES[ 'userpic-file-photo' ][ 'error' ]) {
                if ( !in_array($_FILES[ 'userpic-file-photo' ][ 'type' ], $fileTypes)) {
                    return 'Загрузите корректный тип файла';
                }
            } elseif ($_FILES[ 'userpic-file-photo' ][ 'error' ]) {
                if ( !filter_var($urlValue, FILTER_VALIDATE_URL)) {
                    return 'Введите корректную ссылку';
                } elseif ( !file_get_contents($urlValue)) {
                    return 'Не удалось загрузить файл';
                } elseif ( !in_array(get_headers($_POST[ 'url' ], 1)[ 'Content-Type' ], $fileTypes)) {
                    return 'Загрузите корректный тип файла';
                }
            }
        } elseif (empty($urlValue)) {
            return "Поле не заполнено";
        } elseif ( !filter_var($urlValue, FILTER_VALIDATE_URL)) {
            return 'Введите корректную ссылку';
        } elseif ($_POST[ 'typeName' ] === 'video' && !is_bool(check_youtube_url($urlValue))) {
            return check_youtube_url($urlValue);
        }

        return '';
    }
