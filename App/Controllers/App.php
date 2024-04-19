<?php

class App 
{
    private $formData = [ 'values' => [], 'errors' => [] ];
    
    public function __construct()
    {
        session_start();
    }
    
    public function actionFormRender()
    {
        $token = $_SESSION['csrf'] = $_SESSION['csrf'] ?? md5(time());
        extract( $this->formData );
        require_once __APP_DIR__ . 'Views/index.php';
    }
    
    public function actionFormProceed()
    {
        $this->_formDataVerify();
        if ( $this->formData['errors'] ){ return $this->actionFormRender(); }
        /*$params = [
            'name'  => 'User Name Some User Name', 
            'curs'  => 'Neque porro quisquam est "qui dolorem" ipsum quia do', 
            'date'  => date('Y-m-d')
        ];*/
        
        $this->formData['values']['date'] = date_create_from_format('d.m.Y', $this->formData['values']['date'])->format('Y-m-d');
        $cert = new Certificate( $this->formData['values'] );
        
        return $cert->addAndDownload();
    }
    private function _formDataVerify()
    {
        // csrf
        if ( ! ($_POST['token'] ?? false) || $_POST['token'] !== ($_SESSION['csrf'] ?? null) ){ self::_redirect('/'); }
        unset( $_SESSION['csrf'] );

        // sanitize
        $params = filter_input_array(INPUT_POST, [
            'name' => FILTER_SANITIZE_SPECIAL_CHARS,
            'curs' => FILTER_SANITIZE_SPECIAL_CHARS,
            'date' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);
        $this->formData['values'] = $params;
        
        // validate Штучний інтелект
        if ( empty($params['name']) || mb_strlen($params['name']) < 3 || mb_strlen($params['name']) > 25 ){ $this->formData['errors']['name'] = "Ім'я має містити від 3 до 25 символів"; }
        if ( empty($params['curs']) || mb_strlen($params['curs']) < 5 || mb_strlen($params['curs']) > 50 ){ $this->formData['errors']['curs'] = "Назва курсу має містити від 5 до 50 символів"; }
        if ( empty($params['date']) || (date_create_from_format('d.m.Y', $params['date']))->format('d.m.Y') !== $params['date'] ){ $this->formData['errors']['date'] = "Вкажіть дату"; }
    }
    
    public function actionCertificateValidate()
    {
        $number = $_GET['number'] ?: '';
        if ( strlen($number) !== 32 ){ header('Location: /404.html'); die(); }

        $cert = Certificate::get( $number );
        if ( $cert ){
            die('<h1 style="text-align:center">Certificate is <i style="color: #2e2;">VALID</i></h1>');
        } else {
            die('<h1 style="text-align:center">Certificate is <i style="color: #e22;">INVALID</i></h1>');
        }
    }
    
    private static function _redirect( $url = '/' )
    {
        header('Location: '.$url); 
        die();
    }
}
