<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>GoedBezig</title>
    <link rel="stylesheet" href="components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cards.css">
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="css/fonts.css">
</head>

<body id="home">


    <div class="page page-login" data-state="logo" style="display: block;">
        <h1 class="logo">Inloggen</h1>

        <div class="form">
            <button class="button-facebook hammer-tappable">Meld aan met Facebook</button>
            <button class="button-foursquare hammer-tappable"><span>Meld aan met</span> <img src="img/foursquare.svg"
                                                                                             alt="foursquare"/></button>
        </div>

        <div class="loader"></div>
    </div>

    <div class="page page-main" data-state="overview">
        <div class="header">
            <div class="wrapper">
                <img class="ditzoLogo" src="img/ditzo_logo.svg">

                <div id="settings" class="hammer-tappable">
                    <img class="cog cog1" src="img/cog.svg"/>
                    <img class="cog cog2" src="img/cog.svg"/>
                    <img class="cog cog3" src="img/cog.svg"/>
                    <img class="settings-frame" src="img/settings-frame.png"/>
                </div>
            </div>
        </div>

        <div class="content content-main">
            <div class="scrollable">
                <div id="card-container" class="wrapper">
                </div>

            </div>
        </div>

        <div class="content overlay"></div>

        <div class="content content-settings">
            <div class="scrollable">
                <div class="wrapper settings-wrapper">
                    <p>Gegevens</p>
                    <input type="text" class="mailadres" name="mailadres" placeholder="Mailadres">
                    <input type="text" class="adres" name="adres" placeholder="Adres">
                    <input type="text" class="woonplaats" name="woonplaats" placeholder="Woonplaats">

                    <p>Meldingen</p>

                    <div class="option-checkable hammer-tappable un-checked">
                        <span>Achievements</span>
                        <input type="checkbox" name="meldingen" value="achievements">
                    </div>
                    <div class="option-checkable hammer-tappable un-checked">
                        <span>Goodies</span>
                        <input type="checkbox" name="meldingen" value="goodies">
                    </div>
                    <p>Medium</p>

                    <div class="option-checkable hammer-tappable un-checked">
                        <span>Mail</span>
                        <input type="radio" name="medium" value="mail">
                    </div>
                    <div class="option-checkable hammer-tappable un-checked">
                        <span>SMS</span>
                        <input type="radio" name="medium" value="sms">
                    </div>
                    <input type="submit" class="uitloggen" value="Uitloggen">
                </div>
            </div>
        </div>


    </div>

    <script type="text/html" id="template-card">
        <div class="card-wrapper">
            <div class="card type-:achievementIcon1" style="height::achievementDataRatio;" data-id=":achievementId">
                <div class="card-front card-front-:achievementIcon2">
                    <div class="card-icon card-front-icon-:achievementIcon3"></div>
                    <div class="goodie-indicator"></div>
                </div>
                <div class="card-back">
                    <div class="card-header">
                        <div class="card-icon card-back-icon-:achievementIcon4"></div>
                        <div class="goodie-indicator"></div>
                        <span class="card-title">:achievementTitle</span>
                        <span class="card-description">:achievementDescription</span>
                        <i class="card-close icon-remove"></i>
                    </div>

                    <div class="stamp-wrapper clearfix">
                        <!--:achievementStamps-->
                    </div>
                </div>
            </div>
        </div>
    </script>


    <script>
        window.config = {
            foursquare_id: <?= json_encode($foursquare_client_id); ?>
        };
    </script>
    <script src="components/jquery/jquery.min.js"></script>
    <script src="components/jsUri/Uri.js"></script>
    <script src="components/hammer/jquery.hammer.min.js"></script>
    <script src="components/masonry/jquery.masonry.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/tappable.js"></script>

</body>
</html>
