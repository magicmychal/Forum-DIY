<?php

require_once ('routes.php');

function __autoload($class_name) {
    if (file_exists('./includes/classes/'.$class_name.'.php')){
        require_once './includes/classes/'.$class_name.'.php';
    } else if (file_exists(require_once './includes/Controllers/'.$class_name.'.php')) {
        require_once './includes/controllers/'.$class_name.'.php';
    }

}