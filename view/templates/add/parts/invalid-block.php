<?php
    /**
     * @var $errors
     */
?>

<div class="form__invalid-block">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php foreach ($errors as $error): ?>
            <li class="form__invalid-item"><?= $error['name'] ?>. <?= $error['description'] ?></li>
        <?php endforeach; ?>
    </ul>
</div>
