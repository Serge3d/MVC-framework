<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $message): ?>
        <div class="mx-auto mt-3 msg<?php if ($message['login'] == $login) echo ' msg-send'; ?>">
            <div class='msg-meta'>
                <?php echo "{$message['nicename']} : {$message['send']}"; ?>
            </div>
            <div class='msg-content'>
                <?php echo nl2br($message['text']); ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>