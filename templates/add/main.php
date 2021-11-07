<div class="page__main-section">
    <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
    </div>
    <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
            <div class="adding-post__tabs filters">
                <ul class="adding-post__tabs-list filters__list tabs__list">
                    <?php foreach ($contentTypes as $key => $value): ?>
                        <li class="adding-post__tabs-item filters__item">
                            <a class="adding-post__tabs-link filters__button filters__button--<?= $value[ 'type_class' ]; ?> tabs__item button<?= activePopularFilterHandler($tabTypeId,
                                $value[ 'id' ]); ?>" href="?type_id=<?= $value[ 'id' ]; ?>">
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $value[ 'type_class' ]; ?>"></use>
                                </svg>
                                <span><?= $value[ 'type_name' ]; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="adding-post__tab-content">
                <?php foreach ($contentTypes as $key => $value): ?>
                    <?php echo include_template('add/' . $value[ 'type_class' ] . '.php', [
                        'isActive' => (int)$value[ 'id' ] === (int)$tabTypeId,
                        'typeClass' => $value[ 'type_class' ],
                        'typeId' => $tabTypeId,
                        'errors' => $errors,
                    ]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
