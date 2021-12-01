<?php
    /** @var $post_data */
?>

<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p><?= htmlspecialchars($post_data['content']); ?></p>
            <cite><?= htmlspecialchars($post_data['quote_author']); ?></cite>
        </blockquote>
    </div>
</div>
