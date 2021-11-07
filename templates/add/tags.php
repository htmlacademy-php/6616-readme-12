<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="post-tags">Теги</label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="post-tags" type="text" name="tags" placeholder="Введите теги"
            value="<?= htmlspecialchars($_POST[ 'tags' ] ?? '') ?>">
    </div>
</div>
