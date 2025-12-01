# 🚀 Guía Rápida de Instalación

## ⚠️ Problema Detectado: Python 3.13

Si estás usando **Python 3.13**, necesitas instalar **Rust** para compilar Pydantic 2.x.

## ✅ Soluciones (Elige una)

### Opción 1: Instalar Rust (Recomendado para Python 3.13)

1. **Descargar Rust:**
   - Ve a: https://rustup.rs/
   - Descarga e instala el instalador
   - En Windows, ejecuta `rustup-init.exe`

2. **Reiniciar la terminal** después de instalar Rust

3. **Instalar dependencias:**
   ```bash
   cd api
   pip install -r requirements.txt
   ```

### Opción 2: Usar Python 3.11 o 3.12 (Más fácil)

1. **Instalar Python 3.11 o 3.12:**
   - Descarga desde: https://www.python.org/downloads/
   - Instala Python 3.11 o 3.12

2. **Usar ese Python para el proyecto:**
   ```bash
   # Crear entorno virtual con Python 3.11/3.12
   py -3.11 -m venv venv
   # O
   py -3.12 -m venv venv
   
   # Activar entorno virtual
   venv\Scripts\activate
   
   # Instalar dependencias
   cd api
   pip install -r requirements_python311_312.txt
   ```

### Opción 3: Instalación Automática

Ejecuta el script que detecta tu versión de Python:

```bash
cd api
python install_auto.py
```

Este script:
- Detecta tu versión de Python
- Verifica si Rust está instalado
- Usa el archivo de requirements correcto
- Te guía en caso de problemas

## 📋 Verificar Instalación

Después de instalar, verifica que todo funcione:

```bash
python test_install.py
```

Deberías ver:
```
✅ FastAPI X.X.X
✅ PyMySQL X.X.X
✅ Uvicorn X.X.X
✅ Pydantic X.X.X
✅ python-dotenv
```

## 🏃 Ejecutar la API

Una vez instalado todo:

```bash
# Configurar base de datos (copiar .env.example a .env y editar)
cp .env.example .env

# Ejecutar API
python main.py
```

La API estará disponible en: http://localhost:8000

## ❓ ¿Problemas?

Consulta `SOLUCION_PROBLEMAS.md` para más ayuda.

