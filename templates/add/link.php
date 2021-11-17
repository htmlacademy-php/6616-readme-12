<section class="adding-post__link tabs__content<?= $isActive ? ' tabs__content--active' : ''; ?>">
    <h2 class="visually-hidden">Форма добавления ссылки</h2>
    <form class="adding-post__form form" action="/add.php?type_id=<?= $typeId ?>" method="post">
        <input type="hidden" name="typeName" value="<?= $typeClass ?>">
        <input type="hidden" name="typeId" value="<?= $typeId ?>">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <?php echo include_template('add/heading.php', [
                    'errors' => $errors,
                ]); ?>
                <div class="adding-post__textarea-wrapper form__input-wrapper<?= $errors[ 'link-url' ] ? ' form__input-section--error' : ''; ?>">
                    <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="post-link" type="text" name="link-url" value="<?= htmlspecialchars($_POST[ 'link-url' ] ?? '') ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errors[ 'link-url' ] ?? ''; ?></p>
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
                                case 'link-url':
                                    $errorType = 'Ссылка';
                                    break;
                                case 'tags':
                                    $errorType = 'Теги';
                                    break;
                            } ?>
                            <li class="form__invalid-item"><?= $errorType . ': ' . $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>
