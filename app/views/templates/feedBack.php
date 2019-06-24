<?php
$message = <<<EOT
Сообщение от $name <a href="mailto:$email">$email</a>.
$text
<a href="http://dev-openserver">Перейти на сайт</a>
EOT;
return nl2br($message);