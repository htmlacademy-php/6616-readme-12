-- добавление типов контента для поста;

INSERT INTO content_type (type_name, type_class)
VALUES ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Картинка', 'photo'),
       ('Видео', 'video'),
       ('Ссылка', 'link');

-- добавление списка пользователей;

INSERT INTO user (email, login, password, avatar)
VALUES ('larisa@example.com', 'Лариса', '1ShJqe', 'userpic-larisa.jpg'),
       ('vladik@example.com', 'Владик', 'GuIS15', 'userpic-petro.jpg'),
       ('viktor@example.com', 'Виктор', '56t8Ve', 'userpic-mark.jpg');

-- добавление постов;

INSERT INTO post (title, content, image, video, link, show_count, user_id, content_type_id)
VALUES ('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', '', '', '', 14, 1, 2),
       ('Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', '', '', 33, 2, 1),
       ('Наконец, обработал фотки!', '', 'rock.jpg', '', '', 43, 3, 3),
       ('Моя мечта', '', 'coast.jpg', '', '', 65, 1, 3),
       ('Лучшие курсы', '', '', '', 'www.htmlacademy.ru', 43, 2, 5);

-- добавление комментариев к постам;

INSERT INTO post_comment (content, user_id, post_id)
VALUES ('Сам жду! Интересно узнать, чем все кончится...', 3, 2),
       ('Как красиво! Удачи!', 2, 4);

-- добавление лайка к посту;

INSERT INTO post_like
SET user_id = 2, post_id = 1;

-- добавление подписки на пользователя;

INSERT INTO user_subscription
SET subscriber_id = 1, user_id = 2;

-- получение списка постов с сортировкой по популярности вместе с именами авторов и типом контента;

SELECT p.*, u.login, ct.type_class
FROM post p
       INNER JOIN user u ON p.user_id = u.id
       INNER JOIN content_type ct ON p.content_type_id = ct.id
ORDER BY show_count DESC;

-- получение списка постов для конкретного пользователя;

SELECT * FROM post WHERE user_id = 2;

-- получение списка комментариев для одного поста вместе с именем пользователя;

SELECT pc.id, pc.date_add, pc.content, u.login
FROM post_comment pc
       INNER JOIN user u ON pc.user_id = u.id
       INNER JOIN post p ON pc.post_id = p.id
WHERE post_id = 2;

-- получение количества комментариев к посту;

SELECT COUNT(*) as count FROM post_comment WHERE post_id = 2;

-- получение количества лайков у поста;

SELECT COUNT(*) as count FROM post_like WHERE post_id = 1;

-- получение количества постов у пользователя;

SELECT COUNT(*) as count FROM post WHERE user_id = 2;

-- получение количества подписчиков у пользователя;

SELECT COUNT(*) as count FROM user_subscription WHERE user_id = 2;
