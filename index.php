<?php
    require_once('helpers.php');
    require_once('functions.php');

    $pageContent = include_template('main.php', [
        'popularPosts' => [
            [
                'title' => 'Цитата',
                'type' => 'post-quote',
                'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
                'username' => 'Лариса',
                'avatar' => 'userpic-larisa-small.jpg',
            ],
            [
                'title' => 'Игра престолов	',
                'type' => 'post-text',
                'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
                'username' => 'Владик',
                'avatar' => 'userpic.jpg',
            ],
            [
                'title' => 'Наконец, обработал фотки!',
                'type' => 'post-photo',
                'content' => 'rock-medium.jpg',
                'username' => 'Виктор',
                'avatar' => 'userpic-mark.jpg',
            ],
            [
                'title' => 'Моя мечта',
                'type' => 'post-photo',
                'content' => 'coast-medium.jpg',
                'username' => 'Лариса',
                'avatar' => 'userpic-larisa-small.jpg',
            ],
            [
                'title' => 'Лучшие курсы',
                'type' => 'post-link',
                'content' => 'www.htmlacademy.ru',
                'username' => 'Владик',
                'avatar' => 'userpic.jpg',
            ],
        ],
    ]);

    $pageLayout = include_template('layout.php', [
        'pageContent' => $pageContent,
        'pageTitle' => 'readme: популярное',
        'isAuth' => rand(0, 1),
        'userName' => 'Алексей Зубарев',
    ]);

    print($pageLayout);
