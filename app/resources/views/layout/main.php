<!-- app/resources/views/layout/main.php -->
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $this->section('title'); ?> - Swift Jobs</title>
    <link rel="icon" href="pictures/favicon-32x32.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="/css/style.css">

    <?php echo $this->section('head'); ?>

</head>

<body>

    <?php
    include __DIR__ . '/navbar/navbar.php';
    ?>

    <div style="min-height: 60vh;">
        <?php echo $content; ?>
    </div>

    <?php
    include __DIR__ . '/footer/footer.php';
    ?>

    <?php echo $this->section('scripts'); ?>
    <script src="/js/main.js"></script>

</body>

</html>