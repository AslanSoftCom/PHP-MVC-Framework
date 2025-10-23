<?php

class HomeController extends Controller {

    public function index() {
        $this->view->render('users', 'template.headers', ['title' => 'PHP MVC Framework']);
        $this->view->render('users', 'home.index', ['content' => 'Merhaba! PHP MVC Framework\'e Hoş Geldiniz']);
        $this->view->render('users', 'template.footer');
    }
}
