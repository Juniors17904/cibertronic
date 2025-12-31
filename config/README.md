# Configuración de Base de Datos

## Instalación en Servidor Nuevo

1. **Copia el archivo de ejemplo:**
   ```bash
   cp database.example.php database.php
   ```

2. **Edita `database.php` con tus credenciales reales:**
   - Producción: Credenciales de tu servidor (InfinityFree, etc.)
   - Local: Credenciales de tu XAMPP/MAMP local

3. **database.php NO se subirá a Git** (está protegido en .gitignore)

## ⚠️ IMPORTANTE
- **database.php** = Archivo real con credenciales (NO en Git)
- **database.example.php** = Plantilla sin credenciales (SÍ en Git)
