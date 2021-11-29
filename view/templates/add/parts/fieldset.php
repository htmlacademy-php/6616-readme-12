<?php
    /**
     * @var $type_class
     * @var $forms
     * @var $errors
     */

    foreach ($forms[$type_class] as $fields): foreach ($fields as $key => $field):
        $wrapper_class = $field['type'] === 'textarea' ? 'adding-post__textarea-wrapper form__input-wrapper' :
            'adding-post__input-wrapper form__input-wrapper';
        ?>
        <div class="<?= $wrapper_class; ?><?= isset($errors[$key]) ? ' form__input-section--error' : ''; ?>">
            <label class="adding-post__label form__label" for="<?= $key; ?>">
                <?= $field['title']; ?><?= $field['required'] ? ' <span class="form__input-required">*</span>' : ''; ?>
            </label>
            <div class="form__input-section">
                <?php if ($field['type'] === 'textarea'): ?>
                    <textarea class="adding-post__textarea form__textarea form__input" id="<?= $key; ?>" name="<?= $key; ?>"
                        placeholder="<?= $field['placeholder']; ?>"><?= get_field_value($key); ?></textarea>
                <?php else: ?>
                    <input class="adding-post__input form__input" type="text" id="<?= $key; ?>" name="<?= $key; ?>"
                        placeholder="<?= $field['placeholder']; ?>" value="<?= get_field_value($key); ?>">
                <?php endif; ?>
                <?php if (isset($errors[$key])): echo include_template('add/parts/error.php', [
                    'error_title' => $errors[$key]['title'],
                    'error_description' => $errors[$key]['description'],
                ]); endif; ?>
            </div>
        </div>
    <?php endforeach; endforeach; ?>
