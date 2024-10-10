<?php $this->startSection('title'); ?>
    Strona Główna
<?php $this->endSection(); ?>

<div class="container">
    <h1 class="title"><?php echo $message; ?></h1>
    <p>To jest treść strony głównej.</p>
</div>

<?php $this->startSection('scripts'); ?>
    <!-- <script src="path/to/script.js"></script> -->
<?php $this->endSection(); ?>