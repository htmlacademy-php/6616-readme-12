<?php
    /** @var $post_data */
?>

<div class="post-link__wrapper">
    <a class="post-link__external" href="<?= htmlspecialchars($post_data['link']); ?>" title="Перейти по ссылке">
        <div class="post-link__info-wrapper">
            <div class="post-link__icon-wrapper">
                <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($post_data['link']); ?>" alt="Иконка">
            </div>
            <div class="post-link__info">
                <h3><?= htmlspecialchars($post_data['title']); ?></h3>
            </div>
        </div>
        <span><?= htmlspecialchars($post_data['link']); ?></span>
    </a>
</div>
