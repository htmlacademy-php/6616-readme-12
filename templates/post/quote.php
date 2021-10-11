<div class="post-details__image-wrapper post-quote">
    <div class="post__main">
        <blockquote>
            <p><?= $post[ 'content' ]; ?></p>
            <cite><?= htmlspecialchars($post[ 'quote_author' ]) ? htmlspecialchars($post[ 'quote_author' ]) :
                    'Неизвестный автор'; ?></cite>
        </blockquote>
    </div>
</div>
