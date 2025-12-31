<?php
// ⚠️ INSTRUCCIONES:
// 1. Copia este archivo y renómbralo a: database.php
// 2. Reemplaza los valores con tus credenciales reales
// 3. database.php NO se subirá a Git (está en .gitignore)

return [
    'production' => [
        'host' => 'tu_host_de_produccion',
        'dbname' => 'tu_nombre_de_bd',
        'username' => 'tu_usuario',
        'password' => 'tu_contraseña'
    ],
    'local' => [
        'host' => 'localhost',
        'dbname' => 'cibertronicbd',
        'username' => 'root',
        'password' => ''
    ]
];
