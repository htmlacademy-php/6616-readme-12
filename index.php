<?php
    require_once('config.php');
    require_once('helpers.php');
    require_once('functions.php');

    $filterTypeId = filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    $connection = getConnection(DBHOST, DBUSER, DBPASSWORD, DBNAME);
    $posts = getPosts($connection, $filterTypeId);
    $contentTypes = getContentTypes($connection);

    $pageContent = include_template('popular/main.php', [
        'connection' => $connection,
        'posts' => $posts,
        'contentTypes' => $contentTypes,
        'filterTypeId' => $filterTypeId,
    ]);

    $pageLayout = include_template('popular/layout.php', [
        'pageContent' => $pageContent,
        'pageTitle' => 'readme: популярное',
        'isAuth' => rand(0, 1),
        'userName' => 'Алексей Зубарев',
    ]);

    print($pageLayout);
