<?php
    /** @var $post_data */
?>

<div class="post-details__image-wrapper post-photo__image-wrapper">
    <?= embed_youtube_video(htmlspecialchars($post_data['video'])); ?>
</div>
