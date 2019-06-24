<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto" id="chatcontainer">
            <div class="row">
                <div class="col-2">
                    <a href="/chats">Назад</a>
                </div>
                <div class="col chat-name">
                    <?php echo $chat['name']; ?>
                </div>                                
            </div>
            <div class="chat" id="chat" data-chatid="<?php echo $chat['chat_id']; ?>"></div>
            <div class="chat-form" id="chatform">
                <form action="/send/<?php echo $chat['chat_id']; ?>" method="post" id="send">
                    <div class="row">
                        <div class="col">
                            <textarea class="form-control" name="text" rows="1" placeholder="Ваше сообщение" id="sendtextarea"></textarea>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary" id="sendMessageButton">Отправить</button>
                        </div>
                    </div>
                </form>                
            </div>
        </div>
    </div>
</div>