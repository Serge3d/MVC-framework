$(document).ready(function() {
    // Отправка сообщения
    $('#send').submit(function(event) {
        var json;
        event.preventDefault();
        var message = $('#sendtextarea').val().trim();
        if (message.length == 0) {
            return false;
        }
        $('#sendtextarea').val(message);
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(result) {
                json = jQuery.parseJSON(result);
                if (json.status == "error") {
                    alert(json.message);
                } else {
                    $('#sendtextarea').val('');
                    $('#sendtextarea').keyup();
                    getNewMessage();
                }
            },
        });
    });   
    // Функция получения новых сообщений
    function getNewMessage() {
        // Сбрасываем таймер для исключения дублирования
        clearTimeout(timerId);
        var json;
        $.ajax({
            type: "POST",
            url: "/get/" + chatId,
            data: { lastMessageId: lastMessageId },
            success: function(result) {
                json = jQuery.parseJSON(result);
                if (json.html != false) {
                    lastMessageId = json.lastMessageId;
                    var chat = $("#chat");
                    // Опеределяем, нижнее ли положение прокрутки окна чата: прокрутка >= высота содержимого - высота окна - 10
                    var scroll = (chat.scrollTop() >= chat.prop('scrollHeight') - chat.innerHeight() - 10);
                    chat.html(chat.html() + json.html);
                    // Если окно чата было промотано вниз, перематываем его еще раз вниз для отображения новых сообщений
                    if (scroll) {
                        chat.animate({ scrollTop: (10 + chat.prop('scrollHeight') - chat.innerHeight())}, 500);
                        // chat.scrollTop(chat.prop('scrollHeight'));
                    }
                }
            },
        });
        // Запускаем повторно функцию через 5 секунд
        timerId = setTimeout(function() { getNewMessage() }, 5000);
    }
    // Функция получения старых сообщений
    function getOldMessage() {  
        if ($("#chat").scrollTop() > 200) {
            return;
        }     
        var json;
        $.ajax({
            type: "POST",
            url: "/get/" + chatId,
            data: { firstMessageId: firstMessageId },
            success: function(result) {
                json = jQuery.parseJSON(result);
                if (json.html == false) {
                    noOldMessage = true;
                } else {
                    firstMessageId = json.firstMessageId;
                    var chat = $("#chat");
                    var scroll = chat.prop('scrollHeight') - chat.scrollTop();
                    var html = json.html + chat.html();
                    chat.html(html);
                    chat.scrollTop(chat.prop('scrollHeight') - scroll);
                    if (lastMessageId == 0) {
                        lastMessageId = json.lastMessageId;
                        chat.scrollTop(chat.prop('scrollHeight'));
                    }
                }
            },
        });
    }

    // Таймер
    var timerId;
    var chatId = $('#chat').data('chatid');
    var firstMessageId = 0;
    var noOldMessage = false; 
    var lastMessageId = 0;

    // Автоматическое изменение высоты textarea
    $('#sendtextarea').on('keyup paste', function() { 
        var $el = $(this), offset = $el.innerHeight() - $el.height(); 
        if ($el.innerHeight < this.scrollHeight) { 
            //Grow the field if scroll height is smaller 
            $el.height(this.scrollHeight - offset); 
        } else { 
            //Shrink the field and then re-set it to the scroll height in case it needs to shrink 
            $el.height(1); 
            $el.height(this.scrollHeight - offset); 
        } 
    }); 

    // установим обработчик события scroll для подгрузки старых сообщений 
    $('#chat').scroll(function(event){
        // Если новых сообщений больше нет, открепляем обработчик
        if (noOldMessage) {
            $(this).unbind(event);
        }
        getOldMessage1000();
    });

    // Тормозилка выполнения функций
    function throttle(func, ms) {
        var isThrottled = false,
            savedArgs,
            savedThis;
        function wrapper() {
            if (isThrottled) { // (2)
              savedArgs = arguments;
              savedThis = this;
              return;
            }
            func.apply(this, arguments); // (1)
            isThrottled = true;
            setTimeout(function() {
              isThrottled = false; // (3)
              if (savedArgs) {
                wrapper.apply(savedThis, savedArgs);
                savedArgs = savedThis = null;
              }
            }, ms);
        }
        return wrapper;
    }
    // Заторможенная на 1000мс функция подгрузки старых сообщений
    var getOldMessage1000 = throttle(getOldMessage, 1000);

    $('#sendtextarea').keyup();
    // Обновление окна чата после загрузки страницы
    getOldMessage();
    // Запускаем повторно функцию через 5 секунд
    timerId = setTimeout(function() { getNewMessage() }, 5000);
});