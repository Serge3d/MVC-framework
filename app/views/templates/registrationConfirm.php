<?php
$message = <<<EOT
Вы зарегестрировались на <a href="http://dev-openserver">dev.h1n.ru</a> под логином "$login".
Для завершения регистрации перейдите по ссылке: <a href="$url">$url</a>
EOT;
return nl2br($message);