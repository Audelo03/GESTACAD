"""
Script simple para probar la API
"""
import requests

BASE_URL = "http://localhost:5000"

def test_api():
    """Prueba básica de la API"""
    print("Probando API...")
    
    try:
        # Test 1: Endpoint raíz
        print("\n1. Probando endpoint raíz...")
        response = requests.get(f"{BASE_URL}/")
        print(f"   Status: {response.status_code}")
        print(f"   Respuesta: {response.json()}")
        
        # Test 2: Estadísticas
        print("\n2. Probando estadísticas (alumno_id=2)...")
        response = requests.get(f"{BASE_URL}/api/estadisticas/2")
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            print(f"   Asistencia: {data.get('asistencia_promedio')}%")
            print(f"   Calificación: {data.get('calificacion_promedio')}")
        
        # Test 3: Análisis de riesgo
        print("\n3. Probando análisis de riesgo (alumno_id=2)...")
        response = requests.get(f"{BASE_URL}/api/riesgo/2")
        print(f"   Status: {response.status_code}")
        if response.status_code == 200:
            data = response.json()
            print(f"   Nivel de Riesgo: {data.get('nivel_riesgo')}")
            print(f"   Explicación: {data.get('explicacion')}")
        
        print("\n✅ Todas las pruebas completadas!")
        
    except requests.exceptions.ConnectionError:
        print("\n❌ Error: No se pudo conectar a la API")
        print("   Asegúrate de que el servidor esté ejecutándose:")
        print("   python app.py")
    except Exception as e:
        print(f"\n❌ Error: {e}")

if __name__ == "__main__":
    test_api()


