<?php
    /** @var $post_data */
?>

<div class="post-video__block">
    <div class="post-video__preview">
        <?= embed_youtube_cover($post_data['video']); ?>
    </div>
    <a href="/post.php?id=<?= $post_data['id']; ?>" class="post-video__play-big button">
        <svg class="post-video__play-big-icon" width="14" height="14">
            <use xlink:href="#icon-video-play-big"></use>
        </svg>
        <span class="visually-hidden">Запустить проигрыватель</span>
    </a>
</div>
