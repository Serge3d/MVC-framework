<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <h1><?php echo $title; ?></h1>
            <p><?php echo $message; ?></p>
            <?php if (isset($url)): ?>
                <a href="<?php echo $url; ?>"><?php echo $link; ?></a>                       
            <?php endif; ?>            
            <p><a href="/">Перейти на главную</a></p>
        </div>
    </div>
</div>
