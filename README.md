## Aduana

Aduana es una pequeña librería para sanear y filtrar datos de forma segura y simple.

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
           "required" => "ingresar una password",
           "pattern" => "tu password no debe contener saltos de línea ni espacios"
       ]
   ]
];

$shema = new Aduana\Shema($format);

$filter = $shema->filter($_POST)

```

Como notará el ejemplo anterior enseña una instancia de **Aduana\Shema**, este requiere un array que enseñe las propiedades a filtrar con su correspondiente formato

### Aduana\Shema::filter

el método **filter** permite filtrar y sanear datos en base a la instancia de **Aduana\Shema**, este siempre retornara 4 propiedades.

* **valid** : define si sea ha procesado toda la estructura shema de forma correcta, sin valores inválidos.
* **dataValid** : retorna las propiedades validadas.
* **dataInvalid** : retorna las propiedades requeridas e inválidas.
* **countValid** : es un contador de las propiedades válidas
* **countInvalid** : es un contador de las propiedades invalidas

> Se advierte que todas las propiedades inválidas y no requeridas simplemente se ignoran en el resultado de validación.

## Aduana\Filter

Esta clase posee todos los filtros que posee por defecto **Aduana**.

Por defecto todos los métodos encargados de filtrar y sanear son métodos estáticos de 2 argumentos `Aduana\Filter::$filter($config, $value)`.

1. **$config** : define la configuración del filtro.
2. **$value**: el valor a filtrar o sanear por el método.

A su vez todo filtro dentro y fuera de aduana debe retornar un objeto con 2 propiedades en el `(object)["valid"=>boolean, "value"=>any ]`.

### Aduana\Filter::type (string $config, $value)

Filtra un valor en base a su tipo($config) en comparación a su valor($value).

|type|detail|
|----|------|
|**email, mail**| Define si es un email  |
|**integer, int**| Define si es un número entero  |
|**boolean, bool**| Define si es un booleano  |
|**float, double**| Define si es un número flotante  |
|**number, numberic**| Define si es un número |
|**null**| Define si es nulo |
|**infinite**| Define si es un número infinito |
|**finite**|  Define si el número es finito |
|**json**| Define si es un json |
|**url**| Define si es una url |
|**date**| Define si es un fecha |
|**default**| Si no posee una definición de tipo usara la función gettype para verificar el tipo |

```php
$shema = [
   "user_email" => [
       "type"=>"email"
   ]
];
```

### Aduana\Filter::minLength (int $config, string $value)

Requiere un largo mínimo para validar el valor.

```php
$shema = [
   "password" => [
       "minLength" => 6
   ]
];
```

> La propiedad **password** dentro de **$shema**, debe ser mayor o igual a 6 caracteres.

### Aduana\Filter::maxLength (int $config, string $value)

Requiere un largo máximo para validar el valor.

```php
$shema = [
   "password" => [
       "maxLength" => 12
   ]
];
```

> La propiedad **password** dentro de **$shema**, debe ser menor o igual a 12 caracteres.

### Aduana\Filter::min (int $config, int $value)

Define un valor mínimo numerico.

```php
$shema = [
   "age" => [
       "min" => 18
   ]
];
```

> La propiedad **age** dentro de **$shema**, debe ser mayor o igual a 18.

### Aduana\Filter::max (int $config, int $value)

Define un valor mínimo numerico.

```php
$shema = [
   "age" => [
       "max" => 30
   ]
];
```

> La propiedad **age** dentro de **$shema** debe ser menor o igual a 30.

### Aduana\Filter::stripTags ($config, string $value)

aplica la función strip_tags al valor que apunte el cursor de schema.

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

> Como notara puede usar un **booleano**  en la definición de la propiedad **stripTags** para desactivar este filtro, de igual forma puede aplicar un **string** como parámetro secundario para **strip_tags**.

### Aduana\Filter::pattern (string $config, string $value)

Valida si el string es válido al patrón entregado.

```php
$shema = [
   "tag" => [
       "pattern"=>"/[a-z]+/"
   ]
];
```
> La propiedad **tag** debe cumplir con el patron `"/[a-z]+/"` aceptar su valor como valido.

### Aduana\Filter::replace (array $config, string $value)

Permite reemplazar del valor entregado, caracteres a base de una búsqueda, esta búsqueda a su vez puede ser
una expresión regular o una simple cadena simple.

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
> En las propiedades **tag_1** y **tag_2**, se buscará el carácter **.** y se reemplazará por un carácter **@**

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

Verifica que el valor entregado exista dentro de las opciones válidas.

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

Traduce el valor entregado a otro solo si este existe en el índice correspondiente.

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
> La propiedad **field_1** pasará de poseer un valor **a** a **1**.

### Aduana\Filter::date (string $config, string $value)

Da formato de fecha al valor entregado, esta aplica la función **date**.

```php
$shema = [
   "hour" => [
       "date" => "h:i:s A"
   ]
];
```

### Aduana\Filter::htmlEncode (bool $config, string $value)

Aplica la función **htmlentities** al valor entregado.

```php
$shema = [
   "html" => [
       "htmlEncode" => true
   ]
];
```

### Aduana\Filter::htmlDecode (bool $config, string $value)

Aplica la función **html_entity_decode** al valor entregado.

```php
$shema = [
   "html" => [
       "htmlDecode" => true
   ]
];
```

### Aduana\Filter::equal ($config, $value)

Compara con la siguiente expresión `$config === $value`.

```php
$shema = [
   "html" => [
       "equal" => 10
   ]
];
```

### Aduana\Filter::notEqual ($config, $value)

Compara con la siguiente expresión `$config !== $value`.

```php
$shema = [
   "html" => [
       "notEqual" => 10
   ]
];
```


### Aduana\Filter::numberFormat (array $config, float $value)

Aplica la función **number_format** sobre el valor entregado.

```php
$shema = [
   "price" => [
       "numberFormat" => [ 0 , ".",","]
   ]
];
```

### Aduana\Filter::range (array $config, $value )

Utiliza la función **range** para generar un rango y luego verificar si el valor entregado existe dentro de ese rango.

```php
$shema = [
   "price" => [
       "range" => ["a","b"]
   ]
];
```
### Aduana\Filter::round (float $config, float $value)

Aplica la función **round**, sobre el valor entregado.

```php
$shema = [
   "price" => [
       "round" => true
   ]
];
```

### Aduana\Filter::force (string $config, $value)

fuerza el tipo de una variable, los tipos válidos son **integer || int, float || double, string, boolean || bool, unset**

```php
$shema = [
   "price" => [
       "force" => "integer"
   ]
];
```

### Aduana\Filter::quotemeta (bool $config, string $value)

Aplica la función **quotemeta** sobre el valor entregado.

```php
$shema = [
   "price" => [
       "quotemeta" => true
   ]
];
```

### Aduana\Filter::callback (callable $config, $value)

Ejecuta esta línea con los parámetros asignados `call_user_func( $config, $value)`, Para que sea un filtro válido debe cumplir con el formato de retorno para filtros `(object)["valid"=>boolean, "value"=>any ]`.
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

> Esta función siempre debe retornar una objeto con los siguientes parámetros `(object)["valid"=>boolean, "value"=>any ]`