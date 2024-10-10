<!-- app/resources/views/layout/main.php -->
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->section('title') ?: 'Praca Dorywcza'; ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    
</head>
<body>

    <?php
    include __DIR__.'/navbar/navbar.php';
    ?>

    <?php echo $content; ?>

    <?php echo $this->section('scripts'); ?>
</body>
</html>