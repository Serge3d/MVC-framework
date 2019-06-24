<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <h1><a href="/user/<?php echo $login; ?>"><?php echo $login; ?></a></h1>
            <p>Последние посещения:</p>
        </div>
    </div>
</div>

<div class="container">
    <?php if (!empty($sessionList)): ?>
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>IP</th>
                    <th>User agent</th>
                </tr>
            </thead>
            <?php foreach ($sessionList as $session): ?>
                <tbody>
                    <tr>
                        <td><?php echo $session['auth']; ?></td>
                        <td><?php echo $session['IP']; ?></td>
                        <td><?php echo htmlspecialchars($session['agent'], ENT_QUOTES); ?></td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
    <?php endif; ?> 
</div>
                