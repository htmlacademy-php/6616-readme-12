<?php
    /**
     * @var $error_title
     * @var $error_description
     */
?>

<button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
<div class="form__error-text">
    <h3 class="form__error-title"><?= $error_title; ?></h3>
    <p class="form__error-desc"><?= $error_description; ?></p>
</div>
