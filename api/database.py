"""
Conexión simple a la base de datos MySQL
"""
import pymysql
import os
from dotenv import load_dotenv

load_dotenv()

def get_connection():
    """Obtiene una conexión a la base de datos"""
    return pymysql.connect(
        host=os.getenv('DB_HOST', 'localhost'),
        user=os.getenv('DB_USER', 'root'),
        password=os.getenv('DB_PASSWORD', ''),
        database=os.getenv('DB_NAME', 'gestacadv2'),
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

def execute_query(query, params=None):
    """Ejecuta una consulta SELECT"""
    conn = get_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute(query, params)
            return cursor.fetchall()
    finally:
        conn.close()

def execute_one(query, params=None):
    """Ejecuta una consulta SELECT y retorna un solo resultado"""
    conn = get_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute(query, params)
            return cursor.fetchone()
    finally:
        conn.close()




