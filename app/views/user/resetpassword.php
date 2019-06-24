<div class="container">
    <div class="card card-login text-center mx-auto mt-5">
        <div class="card-header">Сброс пароля</div>
        <div class="card-body">
            <form action="/resetpassword/<?php echo $token; ?>" method="post">                
                <div class="form-group">
                    <label>Новый пароль</label>
                    <input class="form-control" type="password" name="password" placeholder="Пароль">
                </div>
                <div class="form-group">
                    <label>Повторите пароль</label>
                    <input class="form-control" type="password" name="passwordconfirm" placeholder="Пароль">
                </div>                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($error as $err): ?>
                            <p><?php echo $err; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Сменить пароль</button>
            </form>
        </div>
    </div>
</div>