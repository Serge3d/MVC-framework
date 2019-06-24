<div class="container">
    <div class="card card-login text-center mx-auto mt-5">
        <div class="card-header">Сброс пароля</div>
        <div class="card-body">
            <form action="/forgotpassword" method="post">                
                <div class="form-group">
                    <label>Эл. почта</label>
                    <input class="form-control" type="email" name="email" placeholder="Эл. почта" value="<?php if (isset($email)) echo $email; ?>">
                </div>
                <?php if (isset($error)): ?>
                	<div class="alert alert-danger" role="alert">
                		<?php foreach ($error as $err): ?>
                            <p> <?php echo $err; ?></p>
                        <?php endforeach; ?>
                	</div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Сбросить пароль</button>
            </form>
        </div>
        <div class="card-footer">
            <a href="/login" class="card-link">Вход</a>
            <a href="/signup" class="card-link">Регистрация</a>
        </div>
    </div>
</div>