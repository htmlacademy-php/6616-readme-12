<?php
    require_once('config.php');
    require_once('helpers.php');
    require_once('functions.php');

    $connection = getConnection(DBHOST, DBUSER, DBPASSWORD, DBNAME);
    $posts = getPosts($connection);
    $contentTypes = getContentTypes($connection);

    $pageContent = include_template('main.php', [
        'posts' => $posts,
        'contentTypes' => $contentTypes,
    ]);

    $pageLayout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'pageTitle' => 'readme: популярное',
        'isAuth' => rand(0, 1),
        'userName' => 'Алексей Зубарев',
    ]);

    print($pageLayout);
