<?php
    /** @var $post_data */
?>

<blockquote>
    <p><?= htmlspecialchars($post_data['content']); ?></p>
    <cite><?= htmlspecialchars($post_data['quote_author']); ?></cite>
</blockquote>
