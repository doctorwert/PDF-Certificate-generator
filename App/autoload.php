<?php
$cls = [
    'Core/Singleton',
    'Core/DB',
    'Core/ModelBase',
    'Libs/TCPDF/tcpdf',
    'Models/Certificate',
    'Controllers/PDF',
    'Controllers/App',
];
foreach( $cls as $cl ){
    require_once __DIR__ . DIRECTORY_SEPARATOR . $cl . '.php';
}