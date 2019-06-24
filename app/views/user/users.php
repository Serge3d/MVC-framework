<div class="container">
    <h1>Список пользователей</h1>
    <?php if (!empty($users)): ?>
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Логин</th>
                    <th>Имя</th>
                    <th>Дата регистрации</th>
                </tr>
            </thead>
            <?php foreach ($users as $user): ?>
                <tbody>
                    <tr>
                        <td>
                            <a href="/user/<?php echo $user['login']; ?>"><?php echo $user['login']; ?></a>
                        </td>
                        <td>
                            <a href="/user/<?php echo $user['login']; ?>"><?php echo $user['nicename']; ?></a>    
                        </td>
                        <td><?php echo $user['registered']; ?></td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
    <?php endif; ?> 
</div>
                