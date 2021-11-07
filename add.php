<?php
    require_once('config.php');
    require_once('helpers.php');
    require_once('functions.php');
    require_once('validation.php');

    $tabTypeId = filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT) ?? 1;

    $connection = getConnection(DBHOST, DBUSER, DBPASSWORD, DBNAME);
    $contentTypes = getContentTypes($connection);

    $errors = [];
    $rules = [
        'heading' => function () {
            return validateTitle('heading');
        },
        'text' => function () {
            return validateText('text');
        },
        'author' => function () {
            return validateAuthor('author');
        },
        'url' => function () {
            return validateUrl('url');
        },
    ];

    if (count($_POST) > 0) {
        $title = $_POST[ 'heading' ] ?? '';
        $text = $_POST[ 'text' ] ?? '';
        $author = $_POST[ 'author' ] ?? '';
        $video = $_POST[ 'typeName' ] === 'video' ? $_POST[ 'url' ] : '';
        $link = $_POST[ 'typeName' ] === 'link' ? $_POST[ 'url' ] : '';
        $image = '';
        $tags = [];
        $userId = '2';
        $typeId = $_POST[ 'typeId' ];

        foreach ($_POST as $key => $value) {
            if (isset($rules[ $key ])) {
                $rule = $rules[ $key ];
                $errors[ $key ] = $rule();
            }

            if ($key === 'tags' && !empty($value)) {
                $tagsArray = explode(' ', $value);
                $tags = array_diff($tagsArray, array(''));
            }
        }

        $errors = array_filter($errors);

        if (empty($errors)) {
            if (isset($_FILES[ 'userpic-file-photo' ])) {
                if ($_FILES[ 'userpic-file-photo' ][ 'name' ]) {
                    $fileName = $_FILES[ 'userpic-file-photo' ][ 'name' ];
                    $filePath = __DIR__ . '/uploads/';
                    $image = '../uploads/' . $fileName;
                    move_uploaded_file($_FILES[ 'userpic-file-photo' ][ 'tmp_name' ], $filePath . $fileName);
                } else {
                    $uploadedFile = file_get_contents($_POST[ 'url' ]);
                    $fileName = explode('.', parse_url(basename($_POST[ 'url' ]))[ 'path' ])[ 0 ];
                    $fileExtension = explode('/', get_headers($_POST[ 'url' ], 1)[ 'Content-Type' ])[ 1 ];
                    $filePath = __DIR__ . '/uploads/';
                    $fullPath = $filePath . $fileName . '.' . $fileExtension;
                    $image = '../uploads/' . $fileName . '.' . $fileExtension;
                    file_put_contents($fullPath, $uploadedFile);
                }
            }

            $sqlPost = 'INSERT INTO post (title, content, quote_author, image, video, link, user_id, content_type_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?);';

            mysqli_stmt_execute(db_get_prepare_stmt($connection, $sqlPost, [$title, $text, $author, $image, $video, $link, $userId, $typeId]));

            $newPostId = mysqli_insert_id($connection);

            if ( !empty($tags)) {
                $tagsIds = [];
                $oldTags = getTags($connection);

                foreach ($oldTags as $oldTag) {
                    foreach ($tags as $tagIndex => $tagValue) {
                        if ($oldTag[ 'name' ] === $tagValue) {
                            array_push($tagsIds, intval($oldTag[ 'id' ]));
                            unset($tags[ $tagIndex ]);
                        }
                    }
                }

                if ( !empty($tags)) {
                    $insertTags = [];
                    $addedTags = [];

                    foreach ($tags as $tag) {
                        array_push($insertTags, '(?)');
                        array_push($addedTags, '?');
                    }

                    $insertTagsSql = 'INSERT INTO hashtag (name) VALUES ' . implode(',', $insertTags);
                    mysqli_stmt_execute(db_get_prepare_stmt($connection, $insertTagsSql, $tags));

                    $addedTagsSql = 'SELECT id, name FROM hashtag WHERE name IN (' . implode(',', $addedTags) . ')';
                    $stmt = db_get_prepare_stmt($connection, $addedTagsSql, $tags);
                    mysqli_stmt_execute($stmt);
                    $addedTagsResult = mysqli_stmt_get_result($stmt);
                    $addedTagsList = mysqli_fetch_all($addedTagsResult, MYSQLI_ASSOC);

                    foreach ($addedTagsList as $addedTag) {
                        array_push($tagsIds, $addedTag[ 'id' ]);
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
