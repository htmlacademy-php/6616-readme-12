<?php
    /**
     * @var $filter_type_id
     * @var $content_types
     * @var $posts
     */
?>

<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <li class="sorting__item sorting__item--popular">
                    <a class="sorting__link sorting__link--active" href="#">
                        <span>Популярность</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link" href="#">
                        <span>Лайки</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link" href="#">
                        <span>Дата</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
        <div class="popular__filters filters">
            <b class="popular__filters-caption filters__caption">Тип контента:</b>
            <ul class="popular__filters-list filters__list">
                <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                    <a class="filters__button filters__button--ellipse filters__button--all<?= $filter_type_id === 0 ? ' filters__button--active' :
                        ''; ?>"
                        href="/">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($content_types as $key => $value): ?>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $value['type_class'] ?><?= $filter_type_id === $value['id'] ?
                            ' filters__button--active' : ''; ?> button" href="?type_id=<?= $value['id'] ?>">
                            <span class="visually-hidden"><?= $value['type_name'] ?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $value['type_class'] ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="popular__posts">
        <?php foreach ($posts as $key => $value): $type_class = htmlspecialchars($value['type_class']) ?>
            <article class="popular__post post<?= $type_class ? ' post-' . $type_class : '' ?>">
                <header class="post__header">
                    <h2><a href="/post.php?id=<?= $value['id']; ?>"><?= htmlspecialchars($value['title']); ?></a></h2>
                </header>
                <div class="post__main">
                    <?php echo include_template('popular/types/' . $type_class . '.php', [
                        'post_data' => $value,
                    ]); ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" src="view/img/<?= htmlspecialchars($value['avatar']); ?>"
                                    alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= htmlspecialchars($value['login']); ?></b>
                                <time class="post__time" datetime="<?= $value['date_add']; ?>" title="<?= date('d.m.Y H:i',
                                    strtotime($value['date_add'])); ?>"><?= get_relative_data($value['date_add']); ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= htmlspecialchars($value['likes_count']); ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= htmlspecialchars($value['comments_count']); ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</div>
