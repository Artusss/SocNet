<?php
require_once '../includes/model.php';

if(isset($_SESSION['logged_user'])){
    if(isset($_POST['doGoLogout'])){ //выход из учетной записи
        header('Location: ../index.php');
        unset($_SESSION['logged_user']);
        exit();
    }
}
