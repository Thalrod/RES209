<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= WEBROOT . 'css/login.css' ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/d33c0a6991.js" crossorigin="anonymous"></script>
    <?php
    if (isset($data["js"])) {
        foreach ($data["js"] as $js) {
            echo '<script defer src="' . $js . '"></script>';
        }
    } ?>
    <title><?php echo $t ?></title>

</head>

<body>
    <?php
    echo $content;
    ?>
</body>

</html>