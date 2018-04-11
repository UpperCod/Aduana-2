<?php namespace Aduana;

class Response{
    public $value;
    public $valid;
    function __construct($valid, $value, $from = ""){
        $this->value = $value;
        $this->valid = $valid;
        $this->from = $from;
    }
}

class Filter
{
    public $value;
    public $valid;
    public $required;
    public $fill = null;
    function __construct (array $shema, $value ) 
    {
        $valid = false;
        $required = $shema["required"] ?? false;
        $message  = $shema["message"] ?? [];
        $fill  = $message["*"] ?? $this->fill;
        $value = $value ?? $shema["default"] ?? null;

        if( is_null($value) ){
            if ( $required  ){
                $value = $message["required"] ?? $fill ;
            }
        }else{
            $filter = $this->mapMethods($shema, $value);
            if( $filter->valid ){
                $valid = true;
                $value = $filter->value;
            }else{
                $value = $message[$filter->from] ?? $fill ;
            }
        }
        
        $this->value = $value;
        $this->valid = $valid;
        $this->required = $required;
    }
    function mapMethods (array $shema, $value)
    {
        foreach ($shema as $method => $config) {
            switch( $method ){
                case "required":
                case "default":
                case "message":
                    continue;                
                default:
                    if ( method_exists($this,$method) ) {
                        $filter = $this::$method(
                            $config,
                            $value
                        );
                        if ($filter->valid) {
                            $value  = $filter->value;
                        }else{
                            return new Response(false,$value,$method);
                        }
                    }
            }
        }
        return new Response(true,$value);
    }
    static function type (string $config, $value)  
    {
        $valid = false;
        switch($config){
            case "email":
            case "mail":
                $valid = filter_var($value, FILTER_VALIDATE_EMAIL);
                break;
            case "integer":
            case "int":
                $valid = is_integer($value);
                break;
            case "boolean":
            case "bool":
                $valid = is_bool($value);
                break;
            case "float":
            case "double":
                $valid = is_float($value);
                break;
            case "number":
            case "numeric":
                $valid = is_numeric($value);
                break;
            case "null":
                $valid = is_null($value);
                break;
            case "infinite":
                $valid = is_infinite($value);
                break;
            case "finite":
                $valid = is_finite($value);
                break;
            case "json":
                $valid = is_string($value) ? is_array(json_decode($value,true))  : false;
                break;
            case "url":
                $valid = filter_var($value, FILTER_VALIDATE_URL);
                break;
            case "date":
                $valid = strtotime($value) !== false;
                break;
            default:
                $valid = gettype($value) === $config;
        }
        return new Response(
            $valid,
            $value
        );
    }
    static function minLength (int $config, string $value)
    {
        
        return new Response(
            strlen($value) >= $config,
            $value
        );
    }
    static function maxLength (int $config, string $value)
    {
        return new Response(
            strlen($value) <= $config,
            $value
        );
    }
    static function min (int $config, int $value) 
    {
        return new Response(
            $value <= $config,
            $value
        );
    }
    static function max (int $config, int $value) 
    {
        return new Response(
            $value <= $config,
            $value
        );
    }
    static function stripTags ($config, string $value) 
    {
        return new Response(
            true,
            is_string($config) ? strip_tags($value,$config) : (
                is_bool($config) && $config ? strip_tags($value) : $value
            )
        );  
    }
    static function pattern (string $config, string $value) 
    {
        return new Response(
            preg_match($config, $value),
            $value
        );
    }
    static function replace (array $config, string $value)
    {
        return new Response(
            true,
            preg_match("/^\/(.+){1,}\/$/",$config[0]) ?  
                preg_replace($config[0], $config[1], $value) 
                :str_replace($config[0], $config[1], $value)
        );
    }
    static function cleanSpace (bool $config, string $value)
    {
        return new Response(
            true,
            trim(preg_replace("/[\s]+/"," ",$value))
        );
    }
    static function option (array $config, $value) 
    {
        return new Response(
            in_array($value, $config),
            $value
        );
    }
    static function alias (array $config, $value) 
    {
        $exist = $config[$value] ?? false;
        return new Response(
            $exist,
            $exist ? $config[$value] : $value
        );
    }
    static function date (string $config, string $value)
    {
        $valid = strtotime($value);
        return new Response(
            $valid,
            $valid ? date($config, $valid) : $value
        );
    }
    static function htmlEncode (bool $config, string $value)
    {
        return new Response(
            true,
            $config ? htmlentities($value) : $value
        );
    }
    static function htmlDecode (bool $config, string $value)
    {
        return new Response(
            true,
            $config ? html_entity_decode($value) : $value
        );
    }
    static function equal ($config, $value)
    {
        return new Response(
            $config === $value,
            $value
        );
    }
    static function notEqual ($config, $value) 
    {
        return new Response(
            $config !== $value,
            $value
        );
    }
    static function numberFormat (array $config, float $value) 
    {
        return new Response(
            true,
            call_user_func("number_format", $value , ... $config )
        );
    }
    static function range (array $config, $value )
    {
        $range = call_user_func("range",...$config);
        return new Response(
            in_array($value,$range),
            $value
        );
    }
    static function round (float $config, float $value)
    {
        return new Response(
            true,
            round($value)
        );
    }
    static function force (string $config, $value)
    {
        switch($config){
            case "integer":
            case "int":
                $value = (int) $value;
                break;
            case "float":
            case "double":
                $value = (float) $value;
                break;
            case "string":
                $value = (string) $value;
                break;
            case "bool":
            case "boolean":
                $value = (bool) $value;
                break;
            case "unset":
                $value = (unset) $value;
                break;
        }
        return new Response(
            true,
            $value
        );
    }
    static function quotemeta (bool $config, string $value)
    {
        return new Response(
            true,
            $config ? quotemeta($value) : $value
        );
    }
    static function callback (callable $config, $value)
    {
        return call_user_func($config,$value);
    }
}