<div class="container">
    <div class="card card-login text-center mx-auto mt-5">
        <div class="card-header">Регистрация</div>
        <div class="card-body">
            <form action="/signup" method="post">
                <div class="form-group">
                    <label>Логин</label>
                    <input class="form-control" type="text" name="login" placeholder="Логин" value="<?php if (isset($login)) echo $login; ?>">
                </div>
                <div class="form-group">
                    <label>Эл. почта</label>
                    <input class="form-control" type="email" name="email" placeholder="Эл. почта" value="<?php if (isset($email)) echo $email; ?>">
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input class="form-control" type="password" name="password" placeholder="Пароль">
                </div>
                <?php if (isset($error)): ?>
                	<div class="alert alert-danger" role="alert">
                		<?php foreach ($error as $err): ?>
                            <p> <?php echo $err; ?></p>
                        <?php endforeach; ?>
                	</div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Регистрация</button>
            </form>
        </div> 
        <div class="card-footer">
            <a href="/login" class="card-link">Вход</a>
        </div>       
    </div>
</div>