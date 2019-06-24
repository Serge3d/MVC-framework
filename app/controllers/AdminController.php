<?php

namespace app\controllers;

use app\core\Controller;
use app\lib\Pagination;
use app\models\Main;

class AdminController extends Controller {
    
    public function __construct($route)
    {
        parent::__construct($route);
        $this->view->layout = 'admin';
    }

    public function loginAction() {  
        if (isset($_SESSION['admin'])) {
           $this->view->redirect('admin/add');
       } 
       if (!empty($_POST)) {
        if (!$this->model->loginValidate($_POST)) {
            $this->view->message('error', $this->model->error[0]);
        }
        $_SESSION['admin'] = true;
        $this->view->location('/admin/add');
    }      
    $this->view->render('Вход');
}

public function logoutAction() { 
    unset($_SESSION['admin']);
    $this->view->redirect('admin');
}

public function addAction() {     
    if (!empty($_POST)) {
        if (!$this->model->postValidate($_POST, $_FILES, 'add')) {
            $this->view->message('error', $this->model->error[0]);
        }               
        $id = $this->model->postAdd($_POST, $_FILES);

        $this->model->postUploadImage($_FILES, $id);
        $this->view->message('success', 'Пост добавлен. id: '.$id);         
    }        
    $this->view->render('Добавить пост');
}

public function editAction() { 
    if (!$this->model->isPostExists($this->route['id'])) {
        $this->view->errorCode(404);
    }
    if (!empty($_POST)) {
        if (!$this->model->postValidate($_POST, $_FILES, 'edit')) {
            $this->view->message('error', $this->model->error[0]);
        }   
        $this->model->postEdit($_POST, $this->route['id']);
        if ($_FILES['img']['tmp_name']) {
            $this->model->postUploadImage($_FILES, $this->route['id']);
        }
        $this->view->message('success', 'Изменения внесены');         
    }
    $vars = [
        'data' => $this->model->postData($this->route['id'])[0],
    ];
    $this->view->render('Редактировать пост', $vars);
}

public function deleteAction() { 
    if (!$this->model->isPostExists($this->route['id'])) {
        $this->view->errorCode(404);
    }
    $this->model->postDelete($this->route['id']);
    $this->view->redirect('admin/posts');
}

public function postsAction() {  
    $mainModel = new Main;    
    $pagination = new Pagination($this->route, $mainModel->postsCount());
    $vars = [
        'pagination' => $pagination->get(),
        'list' => $mainModel->postsList($this->route),
    ];  
    $this->view->render('Посты', $vars);
}

}