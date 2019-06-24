<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-header"><?php echo $title; ?></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <form class="ajax" enctype="multipart/form-data" action="/admin/add" method="post">
                            <div class="form-group">
                                <label>Название</label>
                                <input class="form-control" type="text" name="name">
                            </div>
                            <div class="form-group">
                                <label>Описание</label>
                                <input class="form-control" type="text" name="description">
                            </div>
                            <div class="form-group">
                                <label>Текст</label>
                                <textarea class="form-control" rows="3" name="text"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Изображение</label>
                                <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
                                <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
                                <input class="form-control" type="file" name="img">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Добавить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>