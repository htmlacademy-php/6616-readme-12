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
        $image = '';
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
            'video-url' => function ($value) {
                return validateVideoUrl($value);
            },
            'tags' => function ($value) {
                return validateTags($value);
            },
            'photo-url' => function ($value) {
                return validatePhotoUrl($value);
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

        if (!empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            $errors['file'] = validateFile($file['tmp_name']);
        }

        $errors = array_filter($errors);

        if (count($errors) === 0) {
            if (!empty($_FILES['file']['name'])) {
                $file = $_FILES['file'];
                $fileType = getFileType($file['tmp_name']);
                $filename = uniqid() . getFileExt($fileType);
                $path = 'uploads/' . $filename;
                move_uploaded_file($file['tmp_name'], $path);
                $image = $path;
            } elseif ($photo) {
                $tmpPath = saveFileFromLink($photo);
                $fileType = getFileType($tmpPath);
                $fileExt = getFileExt($fileType);
                $path = $tmpPath . $fileExt;
                rename($tmpPath, $path);
                $image = $path;
            }

            $sqlPost = 'INSERT INTO post (title, content, quote_author, image, video, link, user_id, content_type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?);';

            $result = mysqli_stmt_execute(db_get_prepare_stmt($connection, $sqlPost, [$title, $text, $author, $image, $video, $link, $userId, $typeId]));

            if ($result) {
                $newPostId = mysqli_insert_id($connection);

                if (count($tags) !== 0) {
                    $tagsIds = [];
                    $oldTags = getTags($connection);

                    foreach ($oldTags as $oldTag) {
                        if (in_array($oldTag['name'], $tags)) {
                            $key = array_search($oldTag['name'], $tags);
                            array_push($tagsIds, intval($oldTag['id']));
                            unset($tags[$key]);
                        }
                    }

                    if (count($tags) !== 0) {
                        $insertTags = [];
                        $addedTags = [];

                        foreach ($tags as $tag) {
                            array_push($insertTags, '(?)');
                            array_push($addedTags, '?');
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
                            array_push($tagsIds, $addedTag['id']);
                        }
                    }

                    $insertTagsPosts = [];
                    $postTagsIds = [];

                    foreach ($tagsIds as $tagId) {
                        array_push($insertTagsPosts, '(? , ?)');
                        array_push($postTagsIds, $newPostId);
                        array_push($postTagsIds, $tagId);
                    }

                    $sqlTagsPostsCon = 'INSERT INTO post_hashtag (post_id, hashtag_id) VALUES ' . implode(',', $insertTagsPosts);
                    mysqli_stmt_execute(db_get_prepare_stmt($connection, $sqlTagsPostsCon, $postTagsIds));
                }

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
