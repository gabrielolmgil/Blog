<?php

//iniciar la sesion de manera segura <NO ES NECESARIO>
session_start([
    'cookie_lifetime' => 0, //cookie expira al cerrar el navegador
    'cookie_httponly' => true, //la cookie no es accesible desde js
    'use_strict_mode' => true, //previene la reutilizacion de ids de sesion
    'use_only_cookies' => true, //solo utiliza cookies para gestionar la sesion
    'cookie_secure' => isset($_SERVER['HTTPS']), //cookies solo en https si esta disponible
    'sid_length' => 128 //longitud id session segura
]);


//comprobar que existe una sesion activa
if(isset($_SESSION['user_id'])){
    //redirigir a home/index.php
    header("Location: views/home/index.php");
    exit();
}else{
    //sino hay sesion llevar al login
    header("Location: views/user/login.php");
    exit();
}

?>