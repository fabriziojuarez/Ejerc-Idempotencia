## Idempotencia
Segun su definicion, es la propiedad donde una operacion puede realizarse varias veces sin cambiar el resultado final. Entonces para el contexto de apis se podria decir, que es la caracteristica del sistema en recibir multiples peticiones identicas (ya sea por insistencia del cliente o fallas en la red) y no afectar el resultado esperado
> Ejemplo: 
> En una pasarela de pago, si el cliente presiona dos veces el boton para pagar, el sistema no puede cobrar el doble o generar un doble registro de la compra, solo tiene que procesar la primera vez.

## Cache
Es el sistema de almacenamiento temporal. Laravel permite cambiar el controlador (file, database, redis, memcached) Puedes emplearlo con:
> use Illuminate\Support\Facades\Cache;

## Pasos de instalacion del ejercicio
### Clonar repositorio
```bash
git clone git@github.com:fabriziojuarez/Ejerc-Idempotencia.git
```
### Instalar dependencias y generar una key
```bash
composer install
php artisan key:generate
```
### Definir variables de entorno
Para este ejercicio solo se requieren las siguientes variables
```Dotenv
# Para la conexion a la base de datos
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
# Para guardar el cache
CACHE_STORE=file        # en archivos en storage/framework/cache
# CACHE_STORE=database  # en una tabla en tu BD
# En caso de que desees emplear redis
# CACHE_STORE=redis     # en Redis
# REDIS_CLIENT=predis   # este es cliente que se empleara, por defecto laravel emplea phpredis
# REDIS_HOST=
# REDIS_PASSWORD=
# REDIS_PORT=
```
> Para emplear redis, se debe instalar la libreria predis/predis con:
> composer require predis/predis
### Correr las migraciones
```bash
php artisan migrate
```
> En el caso de que te salga error, crea primero la base de datos en tu gestor y luego corre las migraciones
### Levantar el proyecto
```bash
php artisan serve
```

## Endpoint para el ejercicio
POST **url_de_tu_localhost**/api/payments
| Parametros de entrada | Tipo | Requerido | Caracteristicas |
|-----------------------|------|:---------:|-----------------|
| amount | numeric | ✔️ | min:0 |
| currency | string | ✔️ | size:3 |
| description | string | ❌ |  |

| Headers | Requerido | Valor | Notas |
|---------|:---------:|-------|-------|
| Accept | ✔️ | application/json | Permitira traer los errores en formato json |
| Idempotency-Key | ✔️ | *"cualquier valor"* | Es lo que permitira saber si la peticion esta en el cache |

## Estructura de salida
### Peticion procesada
```json
{
  "success": true,
  "message": "Pago procesado",
  "data": {
    "amount": "...",
    "currency": "...",
    "status": "accepted",
    "description": "...",
    "updated_at": "...",
    "created_at": "...",
    "id": ...
  },
  "replayed": false
}
```
### Peticion recuperada del cache
```json
{
  "success": true,
  "message": "Pago recuperado del cache",
  "data": {
    "amount": "...",
    "currency": "...",
    "status": "accepted",
    "description": "...",
    "updated_at": "...",
    "created_at": "...",
    "id": ...
  },
  "replayed": true
}
```
