-- список типов контента для поста;

INSERT INTO content_type (type_name, type_class)
VALUES ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Картинка', 'photo'),
       ('Видео', 'video'),
       ('Ссылка', 'link');

-- список пользователей;

INSERT INTO user (email, login, password, avatar)
VALUES ('larisa@example.com', 'Лариса', '1ShJqe', 'userpic-larisa-small.jpg'),
       ('vladik@example.com', 'Владик', 'GuIS15', 'userpic.jpg'),
       ('viktor@example.com', 'Виктор', '56t8Ve', 'userpic-mark.jpg');

-- посты;

INSERT INTO post (title, content, image, video, link, show_count, user_id, content_type_id)
VALUES ('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', '', '', '', 14, 1, 2),
       ('Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', '', '', 33, 2, 1),
       ('Наконец, обработал фотки!', '', 'rock-medium.jpg', '', '', 43, 3, 3),
       ('Моя мечта', '', 'coast-medium.jpg', '', '', 65, 1, 3),
       ('Лучшие курсы', '', '', '', 'www.htmlacademy.ru', 43, 2, 5);

-- комментарии к постам;

INSERT INTO post_comment (content, user_id, post_id)
VALUES ('Сам жду! Интересно узнать, чем все кончится...', 3, 2),
       ('Как красиво! Удачи!', 2, 4);

-- лайк к посту;

INSERT INTO post_like SET user_id = 2, post_id = 1;

-- подписаться на пользователя;

INSERT INTO user_subscription SET subscriber_id = 1, user_id = 2;

-- список постов с сортировкой по популярности и вместе с именами авторов и типом контента;

SELECT post.*, user.login, content_type.type_class
FROM post
       INNER JOIN user ON post.user_id = user.id
       INNER JOIN content_type ON post.content_type_id = content_type.id
ORDER BY show_count DESC;

-- список постов для конкретного пользователя;

SELECT * FROM post WHERE user_id = 2;

-- список комментариев для одного поста, в комментариях должен быть логин пользователя;

SELECT post_comment.id, post_comment.date_add, post_comment.content, user.login
FROM post_comment
       INNER JOIN user ON post_comment.user_id = user.id
       INNER JOIN post ON post_comment.post_id = post.id
WHERE post_id = 2;

-- количество комментариев к посту;

SELECT COUNT(*) FROM post_comment WHERE post_id = 2;

-- количество лайков у поста;

SELECT COUNT(*) FROM post_like WHERE post_id = 1;

-- количество постов у пользователя;

SELECT COUNT(*) FROM post WHERE user_id = 2;

-- количество подписчиков у пользователя;

SELECT COUNT(*) FROM user_subscription WHERE user_id = 2;
