<?php
    require_once('config.php');
    require_once('helpers.php');
    require_once('functions.php');
    require_once('validation.php');

    $tabTypeId = filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT) ?? 1;

    $connection = getConnection(DBHOST, DBUSER, DBPASSWORD, DBNAME);
    $contentTypes = getContentTypes($connection);

    $errors = [];

    if (count($_POST) > 0) {
        $title = isset($_POST['heading']) ? htmlspecialchars($_POST['heading']) : '';
        $text = isset($_POST['text']) ? htmlspecialchars($_POST['text']) : '';
        $author = isset($_POST['author']) ? htmlspecialchars($_POST['author']) : '';
        $photo = isset($_POST['photo-url']) ? htmlspecialchars($_POST['photo-url']) : '';
        $link = isset($_POST['link-url']) ? htmlspecialchars($_POST['link-url']) : '';
        $video = isset($_POST['video-url']) ? htmlspecialchars($_POST['video-url']) : '';
        $file = $_FILES['file'] ?? '';
        $tags = [];
        $userId = '2';
        $typeId = $_POST['typeId'];

        $rules = [
            'heading' => function ($value) {
                return validateFilled($value);
            },
            'author' => function ($value) {
                return validateFilled($value);
            },
            'text' => function ($value) {
                return validateFilled($value);
            },
            'link-url' => function ($value) {
                return validateUrl($value);
            },
            'photo-url' => function ($value) {
                return validatePhotoUrl($value);
            },
            'video-url' => function ($value) {
                return validateVideoUrl($value);
            },
            'tags' => function ($value) {
                return validateTags($value);
            },
        ];

        foreach ($_POST as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule($value);
            }

            if ($key === 'tags' && !empty($value)) {
                $tagsArray = explode(' ', $value);
                $tags = array_diff($tagsArray, []);
            }
        }

        if (!empty($file['name'])) {
            $errors['file'] = validateFile($file['tmp_name']);
        }

        $errors = array_filter($errors);

        if (count($errors) === 0) {
            $image = addImageToPost($file, $photo);

            $sqlPost = 'INSERT INTO post (title, content, quote_author, image, video, link, user_id, content_type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';

            $result = mysqli_stmt_execute(db_get_prepare_stmt($connection, $sqlPost,
                [$title, $text, $author, $image, $video, $link, $userId, $typeId]));

            if ($result) {
                $newPostId = mysqli_insert_id($connection);
                $tagsResult = count($tags) > 0 && addTagsToPost($tags, $newPostId, $connection);

                header('Location: post.php?id=' . $newPostId);
            } else {
                $errors[] = 'Ошибка на сервере. Не удалось сохранить пост';
            }
        }
    }

    $pageContent = include_template('add/main.php', [
        'contentTypes' => $contentTypes,
        'tabTypeId' => $tabTypeId,
        'errors' => $errors,
    ]);

    $pageLayout = include_template('add/layout.php', [
        'pageContent' => $pageContent,
        'pageTitle' => 'readme: добавление публикации',
        'isAuth' => rand(0, 1),
        'userName' => 'Алексей Зубарев',
    ]);

    print($pageLayout);
