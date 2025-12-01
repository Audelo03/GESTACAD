<?php
class AsistenciaToken
{
    private $conn;
    private $table = "asistencia_tokens";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Genera un token único para asistencia
     * @return string Token único
     */
    private function generarToken()
    {
        return bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
    }

    /**
     * Crea un nuevo token de asistencia con expiración de 5 minutos
     * @param int $grupo_id ID del grupo
     * @param string $fecha Fecha de la asistencia (Y-m-d)
     * @param int $usuario_id ID del usuario que genera el token
     * @param int|null $tutoria_grupal_id ID de la tutoría grupal (opcional)
     * @return string|false Token generado o false en caso de error
     */
    public function crearToken($grupo_id, $fecha, $usuario_id, $tutoria_grupal_id = null)
    {
        try {
            // Limpiar tokens expirados del mismo grupo y fecha
            $this->limpiarTokensExpirados($grupo_id, $fecha);

            // Generar token único
            $token = $this->generarToken();
            
            // Verificar que el token sea único (muy poco probable que se repita, pero por seguridad)
            while ($this->tokenExiste($token)) {
                $token = $this->generarToken();
            }

            // Calcular expiración (5 minutos desde ahora)
            $expira_en = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            $sql = "INSERT INTO " . $this->table . " 
                    (token, grupo_id, tutoria_grupal_id, fecha, usuario_id, expira_en) 
                    VALUES (:token, :grupo_id, :tutoria_grupal_id, :fecha, :usuario_id, :expira_en)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
            $stmt->bindParam(":tutoria_grupal_id", $tutoria_grupal_id, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(":expira_en", $expira_en);

            if ($stmt->execute()) {
                return $token;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error creating attendance token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida un token y retorna la información si es válido
     * @param string $token Token a validar
     * @return array|false Información del token o false si es inválido/expirado
     */
    public function validarToken($token)
    {
        try {
            $sql = "SELECT * FROM " . $this->table . " 
                    WHERE token = :token 
                    AND usado = 0 
                    AND expira_en > NOW()";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error validating token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marca un token como usado
     * @param string $token Token a marcar como usado
     * @return bool
     */
    public function marcarComoUsado($token)
    {
        try {
            $sql = "UPDATE " . $this->table . " 
                    SET usado = 1 
                    WHERE token = :token";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":token", $token);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error marking token as used: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un token existe
     * @param string $token Token a verificar
     * @return bool
     */
    private function tokenExiste($token)
    {
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE token = :token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Limpia tokens expirados del mismo grupo y fecha
     * @param int $grupo_id ID del grupo
     * @param string $fecha Fecha (Y-m-d)
     */
    private function limpiarTokensExpirados($grupo_id, $fecha)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " 
                    WHERE grupo_id = :grupo_id 
                    AND fecha = :fecha 
                    AND (expira_en < NOW() OR usado = 1)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error cleaning expired tokens: " . $e->getMessage());
        }
    }

    /**
     * Obtiene información del token con datos del grupo
     * @param string $token Token a consultar
     * @return array|false
     */
    public function obtenerInfoCompleta($token)
    {
        try {
            $sql = "SELECT at.*, g.nombre as grupo_nombre, c.nombre as carrera_nombre
                    FROM " . $this->table . " at
                    LEFT JOIN grupos g ON at.grupo_id = g.id_grupo
                    LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                    WHERE at.token = :token 
                    AND at.usado = 0 
                    AND at.expira_en > NOW()";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting token info: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la IP del cliente
     * @return string IP address
     */
    public static function obtenerIP()
    {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Si hay múltiples IPs (proxy), tomar la primera
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        return $ip;
    }

    /**
     * Verifica si una IP está bloqueada
     * @param string $ip_address IP a verificar
     * @return array|false Información del bloqueo o false si no está bloqueada
     */
    public function verificarIPBloqueada($ip_address)
    {
        try {
            // Limpiar bloqueos expirados
            $this->limpiarBloqueosExpirados();

            $sql = "SELECT * FROM asistencia_ip_bloqueos 
                    WHERE ip_address = :ip_address 
                    AND bloqueado_hasta > NOW()
                    ORDER BY bloqueado_hasta DESC
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":ip_address", $ip_address);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : false;
        } catch (Exception $e) {
            error_log("Error checking IP block: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bloquea una IP por 5 minutos
     * @param string $ip_address IP a bloquear
     * @param string $token Token usado (opcional)
     * @return bool
     */
    public function bloquearIP($ip_address, $token = null)
    {
        try {
            $bloqueado_hasta = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            $ultimo_registro = date('Y-m-d H:i:s');

            // Verificar si ya existe un bloqueo para esta IP
            $sql_check = "SELECT id FROM asistencia_ip_bloqueos 
                         WHERE ip_address = :ip_address 
                         AND bloqueado_hasta > NOW()";

            $stmt_check = $this->conn->prepare($sql_check);
            $stmt_check->bindParam(":ip_address", $ip_address);
            $stmt_check->execute();

            if ($stmt_check->fetch()) {
                // Actualizar bloqueo existente
                $sql = "UPDATE asistencia_ip_bloqueos 
                        SET bloqueado_hasta = :bloqueado_hasta,
                            ultimo_registro = :ultimo_registro,
                            token_usado = :token_usado
                        WHERE ip_address = :ip_address 
                        AND bloqueado_hasta > NOW()";
            } else {
                // Crear nuevo bloqueo
                $sql = "INSERT INTO asistencia_ip_bloqueos 
                        (ip_address, bloqueado_hasta, ultimo_registro, token_usado) 
                        VALUES (:ip_address, :bloqueado_hasta, :ultimo_registro, :token_usado)
                        ON DUPLICATE KEY UPDATE 
                        bloqueado_hasta = :bloqueado_hasta,
                        ultimo_registro = :ultimo_registro,
                        token_usado = :token_usado";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":ip_address", $ip_address);
            $stmt->bindParam(":bloqueado_hasta", $bloqueado_hasta);
            $stmt->bindParam(":ultimo_registro", $ultimo_registro);
            $stmt->bindParam(":token_usado", $token);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error blocking IP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia bloqueos expirados
     */
    private function limpiarBloqueosExpirados()
    {
        try {
            $sql = "DELETE FROM asistencia_ip_bloqueos WHERE bloqueado_hasta < NOW()";
            $this->conn->exec($sql);
        } catch (Exception $e) {
            error_log("Error cleaning expired IP blocks: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el tiempo restante de bloqueo en segundos
     * @param string $ip_address IP a verificar
     * @return int Segundos restantes, 0 si no está bloqueada
     */
    public function obtenerTiempoRestanteBloqueo($ip_address)
    {
        try {
            $sql = "SELECT TIMESTAMPDIFF(SECOND, NOW(), bloqueado_hasta) as segundos_restantes
                    FROM asistencia_ip_bloqueos 
                    WHERE ip_address = :ip_address 
                    AND bloqueado_hasta > NOW()
                    ORDER BY bloqueado_hasta DESC
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":ip_address", $ip_address);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? max(0, (int)$result['segundos_restantes']) : 0;
        } catch (Exception $e) {
            error_log("Error getting remaining block time: " . $e->getMessage());
            return 0;
        }
    }
}

