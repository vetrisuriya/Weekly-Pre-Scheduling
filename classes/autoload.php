<?php
spl_autoload_register('class_autoload');

function class_autoload($val) {
    require_once "./classes/".strtolower($val).".class.php";
}
?>