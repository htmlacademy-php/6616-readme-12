<section class="adding-post__photo tabs__content<?= $isActive ? ' tabs__content--active' : ''; ?>">
    <h2 class="visually-hidden">Форма добавления фото</h2>
    <form class="adding-post__form form" action="/add.php?type_id=<?= $typeId ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="typeName" value="<?= $typeClass ?>">
        <input type="hidden" name="typeId" value="<?= $typeId ?>">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <?php echo include_template('add/heading.php', [
                    'errors' => $errors,
                ]); ?>
                <div class="adding-post__input-wrapper form__input-wrapper<?= $errors[ 'url' ] ? ' form__input-section--error' : ''; ?>">
                    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="photo-url" type="text" name="url" placeholder="Введите ссылку">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errors[ 'url' ] ?? ''; ?></p>
                        </div>
                    </div>
                </div>
                <?php echo include_template('add/tags.php', [
                    'errors' => $errors,
                ]); ?>
            </div>
            <?php if ( !empty($errors)): ?>
                <div class="form__invalid-block">
                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                    <ul class="form__invalid-list">
                        <?php foreach ($errors as $key => $error):
                            $errorType = '';
                            switch ($key) {
                                case 'heading':
                                    $errorType = 'Заголовок';
                                    break;
                                case 'url':
                                    $errorType = 'Ссылка из интернета';
                                    break;
                                case 'tags':
                                    $errorType = 'Теги';
                                    break;
                            } ?>
                            <li class="form__invalid-item"><?= $errorType . '. ' . $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="adding-post__input-file-container form__input-container form__input-container--file">
            <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                    <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="userpic-file-photo" title=" ">
                    <div class="form__file-zone-text">
                        <span>Перетащите фото сюда</span>
                    </div>
                </div>
                <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                    <span>Выбрать фото</span>
                    <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                        <use xlink:href="#icon-attach"></use>
                    </svg>
                </button>
            </div>
            <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

            </div>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>
