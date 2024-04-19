<?php
define('__APP_DIR__', realpath(__DIR__.'/../App').'/');

require_once __APP_DIR__ . 'config.php';
require_once __APP_DIR__ . 'autoload.php';

$app = new App();

// validation (GET /certificate/{certificate_id} )
if ( isset($_GET['number']) ){ $app->actionCertificateValidate(); }

// generate (POST /)
if ( !empty($_POST) ){ $app->actionFormProceed(); }

// form show (GET /)
$app->actionFormRender();