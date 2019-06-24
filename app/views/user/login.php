<div class="container">
    <div class="card card-login text-center mx-auto mt-5">
        <div class="card-header">Вход</div>
        <div class="card-body">
            <form action="/login" method="post">
                <div class="form-group">
                    <label>Логин или Email</label>
                    <input class="form-control" type="text" name="login" placeholder="Логин или Email" value="<?php if (isset($login)) echo $login; ?>">
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input class="form-control" type="password" name="password" placeholder="Пароль">
                </div>
                <div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember" checked>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description small text-dark">Запомнить меня</span>
                    </label>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($error as $err): ?>
                            <p> <?php echo $err; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Войти</button>
            </form>
        </div>
        <div class="card-footer">
            <a href="/forgotpassword" class="card-link">Забыли пароль?</a>
            <a href="/signup" class="card-link">Регистрация</a>
        </div>
    </div>
</div>