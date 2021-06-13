# iHomePlannerWebService
La estructura de archivos es la siguiente:
Directorio base privado del servidor->WWW
  Directorio público HTML ->index.php
  Directorio privado UTILS ->config.php
                           ->functions.php


La página index.php recibe como parámetros un nombre de usuario y contraseña que le facilita la aplicación cuando se registra un usuario.
Una vez recibidos, almacena la ip de origen y guarda un log en el servidor por motivos de seguridad, tras ello realiza las operaciones necesarias
en la base de datos, que son crear una nueva base de datos y el usuario correspondiente con permisos de acceso solo a esa base de datos creada.
