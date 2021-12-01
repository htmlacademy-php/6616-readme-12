<?php
    require_once('functions/bootstrap.php');
    require_once('functions/validation.php');
    require_once('functions/add-post.php');
    require_once('model/types.php');
    require_once('model/posts.php');
    require_once('model/hashtags.php');

    /**
     * @var $connection
     * @var $add_post_forms
     */

    $tab_type_id = filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT) ?? 1;

    $content_types = get_content_types($connection);

    $form_data = $_POST ?? null;
    $errors = [];

    $add_post_forms = [
        'text' => [
            'fields' => [
                'post-heading' => [
                    'type' => 'text',
                    'title' => 'Заголовок',
                    'placeholder' => 'Введите заголовок',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-text' => [
                    'type' => 'textarea',
                    'title' => 'Текст поста',
                    'placeholder' => '',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-tags' => [
                    'type' => 'text',
                    'title' => 'Теги',
                    'placeholder' => 'Введите теги',
                    'required' => false,
                    'validate_rule' => 'validate_tags',
                ],
            ],
        ],
        'quote' => [
            'fields' => [
                'post-heading' => [
                    'type' => 'text',
                    'title' => 'Заголовок',
                    'placeholder' => 'Введите заголовок',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-quote-text' => [
                    'type' => 'textarea',
                    'title' => 'Текст цитаты',
                    'placeholder' => '',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-quote-author' => [
                    'type' => 'text',
                    'title' => 'Автор',
                    'placeholder' => 'Укажите автора',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-tags' => [
                    'type' => 'text',
                    'title' => 'Теги',
                    'placeholder' => 'Введите теги',
                    'required' => false,
                    'validate_rule' => 'validate_tags',
                ],
            ],
        ],
        'photo' => [
            'fields' => [
                'post-heading' => [
                    'type' => 'text',
                    'title' => 'Заголовок',
                    'placeholder' => 'Введите заголовок',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-photo-url' => [
                    'type' => 'text',
                    'title' => 'Ссылка из интернета',
                    'placeholder' => '',
                    'required' => false,
                    'validate_rule' => 'validate_photo_url',
                ],
                'post-tags' => [
                    'type' => 'text',
                    'title' => 'Теги',
                    'placeholder' => 'Введите теги',
                    'required' => false,
                    'validate_rule' => 'validate_tags',
                ],
            ],
        ],
        'video' => [
            'fields' => [
                'post-heading' => [
                    'type' => 'text',
                    'title' => 'Заголовок',
                    'placeholder' => 'Введите заголовок',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-video-url' => [
                    'type' => 'text',
                    'title' => 'Ссылка Youtube',
                    'placeholder' => '',
                    'required' => true,
                    'validate_rule' => 'validate_video_url',
                ],
                'post-tags' => [
                    'type' => 'text',
                    'title' => 'Теги',
                    'placeholder' => 'Введите теги',
                    'required' => false,
                    'validate_rule' => 'validate_tags',
                ],
            ],
        ],
        'link' => [
            'fields' => [
                'post-heading' => [
                    'type' => 'text',
                    'title' => 'Заголовок',
                    'placeholder' => 'Введите заголовок',
                    'required' => true,
                    'validate_rule' => 'validate_filled',
                ],
                'post-link' => [
                    'type' => 'text',
                    'title' => 'Ссылка',
                    'placeholder' => '',
                    'required' => true,
                    'validate_rule' => 'validate_url',
                ],
                'post-tags' => [
                    'type' => 'text',
                    'title' => 'Теги',
                    'placeholder' => 'Введите теги',
                    'required' => false,
                    'validate_rule' => 'validate_tags',
                ],
            ],
        ],
    ];

    if (count($form_data)) {
        $user_id = 1;
        $post_file = $_FILES['post-file-photo'] ?? '';
        $exploded_tags = explode(' ', $form_data['post-tags']);
        $post_tags = array_diff($exploded_tags, ['']);
        $post_text_content = $form_data['post-text'] ?? $form_data['post-quote-text'];
        $post_type_class = $form_data['type-class'];

        // Получаем ошибки формы
        $errors = array_filter(get_post_form_errors($form_data, $post_file, $add_post_forms, $post_type_class));

        if (!count($errors)) {
            // Получаем изображение из формы
            $upload_image_path = get_upload_image_path($post_file, $form_data['post-photo-url']);

            // Добавляем пост и получает результат
            $post_add_result = add_post($connection, $form_data['post-heading'], $post_text_content, $form_data['post-quote-author'],
                $upload_image_path, $form_data['post-video-url'], $form_data['post-link'], $user_id, $form_data['type-id']);

            if ($post_add_result) {
                // Получаем id нового поста
                $post_id = mysqli_insert_id($connection);
                // Добавляем теги и получаем результат
                $tags_add_result = count($post_tags) > 0 && get_post_tags_result($connection, $post_tags, $post_id);

                header('Location: post.php?id=' . $post_id);
            } else {
                $errors[] = 'Ошибка на сервере. Не удалось сохранить пост';
            }
        }
    }

    $page_content = include_template('add/main.php', [
        'content_types' => $content_types,
        'tab_type_id' => $tab_type_id,
        'forms' => $add_post_forms,
        'errors' => $errors,
    ]);

    $page_layout = include_template('add/layout.php', [
        'page_content' => $page_content,
        'page_title' => 'readme: добавление публикации',
        'is_auth' => 1,
        'user_name' => 'Алексей Зубарев',
    ]);

    print($page_layout);
