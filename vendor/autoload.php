<?php
    spl_autoload_register(function ($class) {
        require_once BASEPATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$class.'.php';
    });

