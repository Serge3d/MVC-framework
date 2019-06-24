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
                        <?php if (isset($user_login)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/chats">Сообщения</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/profile"><?php echo $user_nicename; ?></a>
                            </li>                          
                            <li class="nav-item">
                                <a class="nav-link" href="/logout">Выход</a>
                            </li> 
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/login" data-toggle="modal" data-target="#modal-auth">Войти</a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link" href="/signup">Регистрация</a>
                            </li> 
                        <?php endif; ?>
                    </ul>
                    </div>
                </div>
            </nav>
            <?php echo $content; ?>
            <hr>
            <footer>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-md-10 mx-auto">
                            <ul class="list-inline text-center">
                                <li class="list-inline-item">
                                    <a href="https://www.youtube.com/user/Shift63770" target="_blank">
                                        <span class="fa-stack fa-lg">
                                            <i class="fa fa-circle fa-stack-2x"></i>
                                            <i class="fa fa-youtube fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://vk.com/id5069781" target="_blank">
                                        <span class="fa-stack fa-lg">
                                            <i class="fa fa-circle fa-stack-2x"></i>
                                            <i class="fa fa-vk fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="/" target="_blank">
                                        <span class="fa-stack fa-lg">
                                            <i class="fa fa-circle fa-stack-2x"></i>
                                            <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                            <p class="copyright text-muted">&copy; 2019, Мой "велосипед" на PHP</p>
                        </div>
                    </div>
                </div>
            </footer>
            <div class="modal fade" id="modal-auth">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <?php include 'app/views/user/login.php' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>