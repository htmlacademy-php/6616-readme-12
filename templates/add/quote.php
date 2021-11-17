<section class="adding-post__quote tabs__content<?= $isActive ? ' tabs__content--active' : ''; ?>">
    <h2 class="visually-hidden">Форма добавления цитаты</h2>
    <form class="adding-post__form form" action="/add.php?type_id=<?= $typeId ?>" method="post">
        <input type="hidden" name="typeName" value="<?= $typeClass ?>">
        <input type="hidden" name="typeId" value="<?= $typeId ?>">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <?php echo include_template('add/heading.php', [
                    'errors' => $errors,
                ]); ?>
                <div class="adding-post__input-wrapper form__textarea-wrapper<?= $errors['text'] ? ' form__input-section--error' : ''; ?>">
                    <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="text"
                            placeholder="Текст цитаты"><?= htmlspecialchars($_POST['text'] ?? '') ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errors['text'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper<?= $errors['author'] ? ' form__input-section--error' : ''; ?>">
                    <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="quote-author" type="text" name="author" value="<?= htmlspecialchars($_POST['author'] ?? '') ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errors['author'] ?? ''; ?></p>
                        </div>
                    </div>
                </div>
                <?php echo include_template('add/tags.php', [
                    'errors' => $errors,
                ]); ?>
            </div>
            <?php if (!empty($errors)): ?>
                <div class="form__invalid-block">
                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                    <ul class="form__invalid-list">
                        <?php foreach ($errors as $key => $error):
                            $errorType = '';
                            switch ($key) {
                                case 'heading':
                                    $errorType = 'Заголовок';
                                    break;
                                case 'text':
                                    $errorType = 'Текст цитаты';
                                    break;
                                case 'author':
                                    $errorType = 'Автор';
                                    break;
                                case 'tags':
                                    $errorType = 'Теги';
                                    break;
                            }
                            if ($error): ?>
                                <li class="form__invalid-item"><?= $errorType . ': ' . $error; ?></li>
                            <?php endif; ?>
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
