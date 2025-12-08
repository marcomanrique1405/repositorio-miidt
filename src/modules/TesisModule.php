<?php

class TesisModule {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtener tesis filtradas por línea de investigación y otros criterios
     */
    public function getTesisByLinea($linea, $busqueda = '', $estado = '', $director = '', $anio = '') {
        $sql = "SELECT 
                    t.id_tesis, 
                    t.titulo, 
                    t.url, 
                    t.portada, 
                    YEAR(t.fecha_registro) AS anio_registro,
                    t.estado,
                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) AS autor_completo,
                    CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) AS director_completo,
                    li.nombre AS linea_investigacion
                FROM tesis t
                LEFT JOIN autor a ON t.matricula = a.matricula
                LEFT JOIN director d ON t.id_director = d.id_director
                LEFT JOIN linea_investigacion li ON a.id_linea = li.id_linea
                WHERE li.nombre = ?";

        $params = [$linea];
        $types = "s";

        if (!empty($busqueda)) {
            $sql .= " AND (
                        t.titulo LIKE ? 
                        OR CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) LIKE ? 
                        OR CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) LIKE ?
                        OR li.nombre LIKE ?
                    )";
            $busqueda_param = "%$busqueda%";
            $params[] = $busqueda_param;
            $params[] = $busqueda_param;
            $params[] = $busqueda_param;
            $params[] = $busqueda_param;
            $types .= "ssss";
        }

        if (!empty($estado)) {
            $sql .= " AND (t.estado = ? OR t.estado = 'Digital y Fisico')";
            $params[] = $estado;
            $types .= "s";
        }

        if (!empty($director)) {
            $sql .= " AND t.id_director = ?";
            $params[] = $director;
            $types .= "i"; // Asumiendo que id_director es entero
        }

        if (!empty($anio)) {
            $sql .= " AND YEAR(t.fecha_registro) = ?";
            $params[] = $anio;
            $types .= "s";
        }

        $sql .= " ORDER BY t.fecha_registro DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false; // O manejar el error
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    /**
     * Obtener lista de directores para una línea de investigación
     */
    public function getDirectoresByLinea($linea) {
        $sql = "SELECT DISTINCT d.id_director,
                CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) AS director_completo
                FROM director d
                INNER JOIN tesis t ON d.id_director = t.id_director
                INNER JOIN autor a ON t.matricula = a.matricula
                INNER JOIN linea_investigacion li ON a.id_linea = li.id_linea
                WHERE li.nombre = ?
                ORDER BY director_completo ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $linea);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Obtener lista de años disponibles para una línea de investigación
     */
    public function getAniosByLinea($linea) {
        $sql = "SELECT DISTINCT YEAR(t.fecha_registro) AS anio
                FROM tesis t
                INNER JOIN autor a ON t.matricula = a.matricula
                INNER JOIN linea_investigacion li ON a.id_linea = li.id_linea
                WHERE li.nombre = ?
                AND t.fecha_registro IS NOT NULL
                ORDER BY anio DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $linea);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
