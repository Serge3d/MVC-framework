<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo $title; ?></title>
        <link href="/public/styles/bootstrap.css" rel="stylesheet">
        <link href="/public/styles/main.css" rel="stylesheet">
        <link href="/public/styles/font-awesome.css" rel="stylesheet">
        <script src="/public/scripts/jquery.js"></script>
        <script src="/public/scripts/form.js"></script>
        <script src="/public/scripts/popper.js"></script>
        <script src="/public/scripts/bootstrap.js"></script>
        <script src="/public/scripts/chat.js"></script>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light justify-content-lg-center" id="mainNav"> 
                <a class="navbar-brand" href="/">Главная</a>
                <button type="button" class="navbar-toggler"  data-toggle="collapse" data-target="#navbarResponsive" aria-controls="#navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/about">Обо мне</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact">Обратная связь</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/users">Пользователи</a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item">
                            <a class="nav-link" href="/chats">Сообщения</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile"><?php echo $user_nicename; ?></a>
                        </li>                          
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">Выход</a>
                        </li>
                    </ul>
                    </div>
                </div>
            </nav>
            <?php echo $content; ?>
            <hr>
        </div>
    </body>
</html>