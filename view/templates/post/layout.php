<?php
    require_once(dirname(__DIR__) . '/layouts/header.php');

    /** @var $page_content */
?>

<main class="page__main page__main--publication">
    <?= $page_content; ?>
</main>

<?php require_once(dirname(__DIR__) . '/layouts/footer.php'); ?>
