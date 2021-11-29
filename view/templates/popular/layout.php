<?php
    require_once(dirname(__DIR__) . '/layouts/header.php');

    /** @var $page_content */
?>

<section class="page__main page__main--popular">
    <?= $page_content; ?>
</section>

<?php require_once(dirname(__DIR__) . '/layouts/footer.php'); ?>
