<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <h1><a href="/user/<?php echo $login; ?>"><?php echo $login; ?></a></h1>
            <p>Сообщения:</p>
        </div>
    </div>
</div>

<div class="container">
    <?php if (!empty($chatList)): ?>
        <table class="table table-hover table-sm">
            <thead>
                <tr>                    
                    <th>Название</th>
                </tr>
            </thead>
            <?php foreach ($chatList as $chat): ?>
                <tbody>
                    <tr>
                        <td>
                            <a href="/chat/<?php echo $chat['chat_id']; ?>"><?php echo $chat['name']; ?></a>
                            <?php if (isset($chat['unread'])) echo $chat['unread']; ?>
                        </td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
    <?php endif; ?> 
</div>
                