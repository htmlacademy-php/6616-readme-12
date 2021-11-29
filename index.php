<?php
    require_once('functions/bootstrap.php');
    require_once('model/types.php');
    require_once('model/posts.php');

    /** @var $connection */

    $filter_type_id = filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT) ?? 0;
    $content_types = get_content_types($connection);
    $posts = get_posts($connection, $filter_type_id);

    $page_content = include_template('popular/main.php', [
        'connection' => $connection,
        'posts' => $posts,
        'content_types' => $content_types,
        'filter_type_id' => $filter_type_id,
    ]);

    $page_layout = include_template('popular/layout.php', [
        'page_content' => $page_content,
        'page_title' => 'readme: популярное',
        'is_auth' => 1,
        'user_name' => 'Алексей Зубарев',
    ]);

    print($page_layout);

