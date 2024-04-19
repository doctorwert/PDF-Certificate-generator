<?php

class Certificate extends ModelBase
{    
    private function _add() : bool
    {
        $res = $this->save();
        if ( ! $res ){ return false; }
        
        $this->number = md5( $this->id . '_' . $this->created_at );
        
        return $this->save();
    }
    
    public function validate() {
        return ( 
                $this->name     ?? false 
            &&  mb_strlen(trim($this->name)) >= 3
            &&  $this->date     ?? false
            &&  $this->curs     ?? false
            &&  ( ! $this->number || mb_strlen($this->number) === 32 )
        );
    }
    
    public function addAndDownload()
    {
        if ( ! $this->_add() ){ throw new \Exception("Error creating Certificate"); }
        
        PDF::certificateGenerateAndDownload( $this );
    }
    
    public static function get( string $number = null )
    {
        if ( ! $number || ! is_string($number) || strlen($number) !== 32 ){ return null; }
        
        return 
            parent::findByArray([ 'number' => $number ])[0] 
                ?? null;
    }
    
}
