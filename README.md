## Aduana

Aduana es una pequeña libreria para sanear y filtrar datos de forma segura y simple.

```php

$format = [
    "password" => [
        "type" => "string",
        "minLength" => 6,
        "maxLength" => 20,
        "required" => true,
        "pattern" => "/[^\s\t\n]+/",
        "message" => [
            "minLength" => "tu password debe ser mayor a o igual a 6 caracteres",
            "maxLength" => "tu password no debe ser mayor o igual a 20 caracteres",
            "required" => "ingresa una password",
            "pattern" => "tu password no debe contener saltos de linea ni espacios"
        ]
    ]
];

$shema = new Aduana\Shema($format);

$filter = $shema->filter($_POST)

```

Como notara el ejemplo anterior enseña una instancia de **Aduana\Shema**, este require un array que enseñe las propiedades a filtrar con su correspondiente formato

## Aduana\Filter

Esta clase posee todos los filtros que posee por defecto **Aduana**.

Por defecto todos los metodos encargados de filtrar y sanear son metodos estaticos de 2 argumentos `Aduana\Filter::$filter($config, $value)`.

1. **$config** : define la configuracion del filtro.
2. **$value**: el valor a filtrar o sanear por el metodo.

A su vez todo filtro dentro y fuera de aduana debe retornar un objeto con 2 propiedades en el `(object)["valid"=>boolean, "value"=>any ]`.

### Aduana\Filter::type (string $config, $value)

Filtra un valor en base a su tipo($config) en comparacion a su valor($value).

|type|detail|
|----|------|
|**email, mail**| Define si es un email  | 
|**integer, int**| Define si es un numero entero  |
|**boolean, bool**| Define si es un booleano  |
|**float, double**| Define si es un numero flotante  | 
|**number, numberic**| Define si es un nuemero |
|**null**| Define si es nulo |
|**infinite**| Define si es un numero infinito |
|**finite**|  Define si el numero es finito |
|**json**| Define si es un json |
|**url**| Define si es una url |
|**date**| Define si es un fecha |
|**default**| Si no posee una definicion de tipo usara la funcion gettype para verificar el tipo |

```php
$shema = [
    "user_email" => [
        "type"=>"email"
    ]
];
```

### Aduana\Filter::minLength (int $config, string $value)

Require un largo minimo para validar el valor.

```php
$shema = [
    "password" => [
        "minLength" => 6
    ]
];
```

> La propiedad **password** dentro de **$shema**, debe ser mayor o igual a 6 caracteres.

### Aduana\Filter::maxLength (int $config, string $value)

Require un largo maximo para validar el valor.

```php
$shema = [
    "password" => [
        "maxLength" => 12
    ]
];
```

> La propiedad **password** dentro de **$shema**, debe ser menor o igual a 12 caracteres.

### Aduana\Filter::min (int $config, int $value) 

Define un valor minimo numerico.

```php
$shema = [
    "age" => [
        "min" => 18
    ]
];
```

> La propiedad **age** dentro de **$shema**, debe ser mayor o igual a 18.

### Aduana\Filter::max (int $config, int $value) 

Define un valor minimo numerico.

```php
$shema = [
    "age" => [
        "max" => 30
    ]
];
```

> La propiedad **age** dentro de **$shema** debe ser menor o igual a 30.

### Aduana\Filter::stripTags ($config, string $value) 

aplica la funcion strip_tags al valor que apunte el cursor de schema.

```php
$shema = [
    "message_1" => [
        "stripTags" => true
    ],
    "message_2" => [
        "stripTags" => "<p>"
    ]
];
```

> Como notara puede usar un **booleano**  en la definicion de la propiedad **stripTags** para desactivar este filtro, de igual forma puede aplicar un **string** como parametro secundario para **strip_tags**.

### Aduana\Filter::pattern (string $config, string $value) 

Valida si el string es valido al patron entregado.

```php
$shema = [
    "tag" => [
        "pattern"=>"/[a-z]+/"
    ]
];
```
> La propiedad **tag** debe cumplir con el patron `"/[a-z]+/"` aceptar su valor como valido.

### Aduana\Filter::replace (array $config, string $value)

Permite replazar del valor entregado, caracteres a base de una busqueda, esta busqueda a su vez puede ser 
una exprecion regular o una simple cadena simple.

```php
$shema = [
    "tag_1" => [
        "replace"=>["/[\.]+/", "_"]
    ],
    "tag_2" => [
        "replace"=>[".", "_"]
    ]
];
```
> En las propiedades **tag_1** y **tag_2**, se buscara el caracter **.** y se remplazara por un caracter **@**

### Aduana\Filter::cleanSpace (bool $config, string $value)

Limpia un valor en su totalidad de espacios adicionales.

```php
$shema = [
    "message" => [
        "cleanSpace"=>true
    ]
];
```

### Aduana\Filter::option (array $config, $value) 

Verifica que el valor entregado exista dentro de las opciones validas.

```php
$shema = [
    "field_1" => [
        "option" => [
            1,2,3,4
        ]
    ]
];
```

### Aduana\Filter::alias (array $config, $value) 

Traduce el valor entregado a otro solo si este existe en el indice correspondiente.

```php
$shema = [
    "field_1" => [
        "alias" => [
            "a"=>1,
            "b"=>2,
            "c"=>3,
        ]
    ]
];
```
> La propiedad **field_1** pasara de poseer un valor **a** a **1**.

### Aduana\Filter::date (string $config, string $value)

Da formato de fecha al valor entregado, esta aplica la funcion **date**.

```php
$shema = [
    "hour" => [
        "date" => "h:i:s A"
    ]
];
```

### Aduana\Filter::htmlEncode (bool $config, string $value)

Aplica la funcion **htmlentities** al valor entregado.

```php
$shema = [
    "html" => [
        "htmlEncode" => true
    ]
];
```

### Aduana\Filter::htmlDecode (bool $config, string $value)

Aplica la funcion **html_entity_decode** al valor entregado.

```php
$shema = [
    "html" => [
        "htmlDecode" => true
    ]
];
```

### Aduana\Filter::equal ($config, $value)

Compara con la siguiente exprecion `$config === $value`.

```php
$shema = [
    "html" => [
        "equal" => 10
    ]
];
```

### Aduana\Filter::notEqual ($config, $value)

Compara con la siguiente exprecion `$config !== $value`.

```php
$shema = [
    "html" => [
        "notEqual" => 10
    ]
];
```


### Aduana\Filter::numberFormat (array $config, float $value) 

Aplica la funcion **number_format** sobre el valor entregado.

```php
$shema = [
    "price" => [
        "numberFormat" => [ 0 , ".",","]
    ]
];
```

### Aduana\Filter::range (array $config, $value )

Utiliza la funcion **range** para generar un rango y luego verificar si el valor entregado existe dentro de ese rango.

```php
$shema = [
    "price" => [
        "range" => ["a","b"]
    ]
];
```
### Aduana\Filter::round (float $config, float $value)

Aplica la funcion **round**, sobre el valor entregado.

```php
$shema = [
    "price" => [
        "round" => true
    ]
];
```

### Aduana\Filter::force (string $config, $value)

fuerza el tipo de una variable, los tipos validos son **integer || int, float || double, string, boolean || bool, unset**

```php
$shema = [
    "price" => [
        "force" => "integer"
    ]
];
```

### Aduana\Filter::quotemeta (bool $config, string $value)

Aplica la funcion **quotemeta** sobre el valor entregado.

```php
$shema = [
    "price" => [
        "quotemeta" => true
    ]
];
```

### Aduana\Filter::callback (callable $config, $value)

Ejecuta esta linea con los parametros asignados `call_user_func( $config, $value)`, Para que sea un filtro valido debe cumplir con el formato de retorno para filtros `(object)["valid"=>boolean, "value"=>any ]`.
```php
$shema = [
    "price" => [
        "callback" => function ($value) {
            return (object) [
                "valid" => true,
                "value" => $value
            ];
        }
    ]
];
```

> Esta funcion siempre debe retornar una objeto con los siguientes parametros `(object)["valid"=>boolean, "value"=>any ]`