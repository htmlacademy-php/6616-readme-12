<?php
    /**
     * @var $tab_type_id
     * @var $content_types
     * @var $forms
     * @var $errors
     */
?>

<div class="page__main-section">
    <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
    </div>
    <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
            <div class="adding-post__tabs filters">
                <ul class="adding-post__tabs-list filters__list tabs__list">
                    <?php foreach ($content_types as $key => $value):
                        $tab_type_class = $value['type_class'];
                        $is_active = (int)$value['id'] === (int)$tab_type_id; ?>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--<?= htmlspecialchars($tab_type_class); ?> tabs__item button<?= $is_active ?
                                ' filters__button--active' : ''; ?>" href="?type_id=<?= $value['id']; ?>">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $tab_type_class; ?>"></use>
                                </svg>
                                <span><?= htmlspecialchars($value['type_name']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="adding-post__tab-content">
                <?php foreach ($content_types as $key => $value):
                    $tab_type_class = $value['type_class'];
                    $is_active = (int)$value['id'] === (int)$tab_type_id;
                    $is_photo = $tab_type_class === 'photo'; ?>
                    <section class="adding-post__<?= htmlspecialchars($tab_type_class); ?> tabs__content<?= $is_active
                        ? ' tabs__content--active' : ''; ?>">
                        <h2 class="visually-hidden">Форма добавления</h2>
                        <form class="adding-post__form form" action="/add.php?type_id=<?= $tab_type_id; ?>"
                            method="post"<?= $is_photo ? ' enctype="multipart/form-data"' : '' ?>>
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <input type="hidden" name="type-id" value="<?= $tab_type_id ?>">
                                    <input type="hidden" name="type-class" value="<?= $tab_type_class ?>">
                                    <?php if ($forms[$tab_type_class]) : echo include_template('add/parts/fieldset.php', [
                                        'type_class' => $tab_type_class,
                                        'forms' => $forms,
                                        'errors' => $errors,
                                    ]); endif; ?>
                                </div>
                                <?php if (count($errors)) : echo include_template('add/parts/invalid-block.php', [
                                    'errors' => $errors,
                                ]); endif; ?>
                            </div>
                            <?php if ($is_photo) : echo include_template('add/parts/dropzone.php'); endif; ?>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
