<?php namespace Aduana;

require_once "filter.php";

class Shema
{
    public $shema ;
    function __construct (array $shema)
    {
        $this->shema = $shema;
    }   
    function filter (array $input) 
    {
        $valid = [];
        $invalid = [];
        $countValid = 0;
        $countInvalid = 0;

        foreach ( $this->shema as $property => $shema ) {
            if ( $shema instanceof self ){
                $filter = $shema->filter($input[$property] ?? []);
                if($filter->countValid){
                    $valid[$property] = $filter->dataValid;
                    $countValid += $filter->countValid;
                }
                if($filter->countInvalid){
                    $invalid[$property] = $filter->dataInvalid;
                    $countInvalid += $filter->countInvalid;
                }
            }else{
                $value = $input[$property] ?? null;
                $filter = new Filter($shema, $value);
                if( $filter->valid ){
                    $valid[$property] = $filter->value;
                    $countValid++;
                }else if( $filter->required ){
                    $invalid[$property] = $filter->value;
                    $countInvalid++;
                }
            }
        }

        return (object) [
            "dataValid" => $valid,
            "dataInvalid" => $invalid,
            "countValid" => $countValid,
            "countInvalid" => $countInvalid,
            "valid" => !$countInvalid
        ];
    }
}
