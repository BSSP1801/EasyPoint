contraseña root: Ba66age

carpeta views:
-todo aquello que pueda ser visible para el usuario

carpeta models:
-todo lo que sea comunicacion programa <-> BBDD

carpeta controllers:
-todo lo que sea funcionalidad privada del programa

carpeta config:
-configuracion privada de la app

Carpeta sql:
-guardar archivo para importa base de datos

Estos dos comandos use en la carpeta raiz para tener las dependencias y el contenedor: 

composer require phpmailer/phpmailer
docker compose up -d --build
