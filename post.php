<?php
    require_once('config.php');
    require_once('helpers.php');
    require_once('functions.php');

    $postId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    $connection = getConnection(DBHOST, DBUSER, DBPASSWORD, DBNAME);
    $post = getPost($connection, $postId);

    if ( !$post) {
        http_response_code(404);
    } else {
        $postComments = getPostComments($connection, $post[ 'id' ]);

        $pageContent = include_template('post/main.php', [
            'post' => $post,
            'postComments' => $postComments,
        ]);

        $pageLayout = include_template('post/layout.php', [
            'pageContent' => $pageContent,
            'pageTitle' => 'readme: публикация',
            'isAuth' => rand(0, 1),
            'userName' => 'Алексей Зубарев',
        ]);

        print($pageLayout);
    }
