<header class="masthead" style="background-image: url('/public/images/postimages/<?php echo $data['id']; ?>.jpg')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="page-heading">
                    <h1><?php echo htmlspecialchars($data['name'], ENT_QUOTES); ?></h1>
                    <span class="subheading"><?php echo htmlspecialchars($data['description'], ENT_QUOTES); ?></span>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="container">
    <div class="row">

    </div>
</div>
<div class="container">
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="/post/<?php echo $idPrevious; ?>">Предыдущая запись</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/">Назад</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/post/<?php echo $idNext; ?>">Следующая запись</a>
        </li>
    </ul>
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <p><?php echo htmlspecialchars($data['text'], ENT_QUOTES); ?></p>
        </div>
    </div>
</div>