<?php

Router::add('users', 'GET', '/', 'HomeController::index');
Router::dispatch();