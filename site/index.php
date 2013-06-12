<?php

require 'config.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>GoedBezig</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body id="home">


    <div class="page page-login" data-state="logo">
        <h1 class="logo">Inloggen</h1>

        <div class="form">
            <button class="button-facebook hammer-tappable">Facebook</button>
            <button class="button-foursquare hammer-tappable">Foursquare</button>
        </div>

        <div class="loader"></div>
    </div>

    <div class="page page-main">
        <h1>Main</h1>
    </div>

    <div class="page page-settings">
        <h1>Settings</h1>
    </div>

    <script>
        window.config = {
            foursquare_id: <?= json_encode($foursquare_client_id); ?>
        };
    </script>
    <script src="components/jquery/jquery.min.js"></script>
    <script src="components/jsUri/Uri.js"></script>
    <script src="components/hammer/jquery.hammer.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/tappable.js"></script>

</body>
</html>
