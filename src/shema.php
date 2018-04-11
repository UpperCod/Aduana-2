<?php namespace Aduana;

require_once "filter.php";

class Status
{
    public $valid = true;
    public $countValid = 0;
    public $countInvalid = 0;
    public $dataValid = [];
    public $dataInvalid = [];

    function setValid(string $field, $value )
    {
        if ( isset($this->dataInvalid[$field]) ) {
            unset($this->dataInvalid[$field]);
            $this->countInvalid = $this->countInvalid - 1;
        }
        if ( !isset($this->dataValid[$field]) ) {
            $this->countValid = $this->countValid + 1;
        }
        $this->valid = !$this->countInvalid;
        $this->dataValid[$field] = $value;
    }
    function setInvalid( string $field, $value)
    {
        if ( isset($this->dataValid[$field]) ) {
            unset($this->dataValid[$field]);
            $this->countValid = $this->countValid - 1;
        }
        if ( !isset($this->dataInvalid[$field]) ) {
            $this->countInvalid = $this->countInvalid + 1;
        }
        $this->valid = false;
        $this->dataInvalid[$field] = $value;
    }
}

class Shema
{
    public $shema ;
    function __construct (array $shema)
    {
        $this->shema = $shema;
    }   
    function filter (array $input) 
    {
        $status = new Status;
        foreach ( $this->shema as $property => $shema ) {
            if ( $shema instanceof self ){
                $filter = $shema->filter($input[$property] ?? []);
                if($filter->countValid){
                    $status->setValid($property,$filter->dataValid);
                    $status->countValid += $filter->countValid;
                }
                if($filter->countInvalid){
                    $status->setInvalid($property,$filter->dataInvalid);
                    $status->countInvalid += $filter->countInvalid;
                }
            }else{
                $value = $input[$property] ?? null;
                $filter = new Filter($shema, $value);
                if ( $filter->valid ) {
                    $status->setValid($property,$filter->value);
                } else if ( $filter->required || $filter->filtered ) {
                    $status->setInvalid($property,$filter->value);
                }
            }
        }

        return $status;
    }
}
