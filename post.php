<?php
    require_once('functions/bootstrap.php');
    require_once('model/comments.php');
    require_once('model/posts.php');

    /** @var $connection */

    $post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    $post = get_post($connection, $post_id);

    if (!$post) {
        http_response_code(404);
    } else {
        $post_comments = get_post_comments($connection, $post['id']);

        $page_content = include_template('post/main.php', [
            'post' => $post,
            'post_comments' => $post_comments,
        ]);

        $page_layout = include_template('post/layout.php', [
            'page_content' => $page_content,
            'page_title' => 'readme: публикация',
            'is_auth' => 1,
            'user_name' => 'Алексей Зубарев',
        ]);

        print($page_layout);
    }
