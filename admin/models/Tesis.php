<?php 
declare(strict_types=1);

final class Tesis
{
    private mysqli $db;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->db = $conn;
    }

    public function buscar(string $query): array
    {
        
        if ($query === '') {

            $sql = "
                SELECT 
                    t.id_tesis,
                    t.titulo,
                    YEAR(t.fecha_registro) AS anio,
                    CONCAT('/repositorio_MIIDT', t.portada) AS imagen,
                    t.estado,

                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno,'')) AS autor,

                    CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno,'')) AS director,

                    l.nombre AS lies

                FROM tesis t
                LEFT JOIN autor a ON a.matricula = t.matricula
                LEFT JOIN director d ON d.id_director = t.id_director
                LEFT JOIN linea_investigacion l ON l.id_linea = a.id_linea

                ORDER BY t.fecha_registro DESC
            ";

            $result = $this->db->query($sql);

            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $sql = "
            SELECT 
                t.id_tesis,
                t.titulo,
                YEAR(t.fecha_registro) AS anio,
                CONCAT('/repositorio_MIIDT', t.portada) AS imagen,
                t.estado,

                CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno,'')) AS autor,

                CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno,'')) AS director,

                l.nombre AS lies

            FROM tesis t
            LEFT JOIN autor a ON a.matricula = t.matricula
            LEFT JOIN director d ON d.id_director = t.id_director
            LEFT JOIN linea_investigacion l ON l.id_linea = a.id_linea

            WHERE 
                t.titulo LIKE ?
                OR CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) LIKE ?
                OR CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', d.apellido_materno) LIKE ?

            ORDER BY t.fecha_registro DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $like = "%$query%";

        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function filtrar(array $filtros): array
    {
        $sql = "
            SELECT 
                t.id_tesis,
                t.titulo,
                YEAR(t.fecha_registro) AS anio,
                CONCAT('/repositorio_MIIDT', t.portada) AS imagen,
                t.estado,

                CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno,'')) AS autor,

                CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno,'')) AS director,

                l.nombre AS lies

            FROM tesis t
            LEFT JOIN autor a ON a.matricula = t.matricula
            LEFT JOIN director d ON d.id_director = t.id_director
            LEFT JOIN linea_investigacion l ON l.id_linea = a.id_linea

            WHERE 1=1
        ";

        $params = [];
        $types = "";

        if (!empty($filtros['anio'])) {
            $sql .= " AND YEAR(t.fecha_registro) = ?";
            $params[] = (int)$filtros['anio'];
            $types .= "i";
        }

        if (!empty($filtros['director'])) {
            $sql .= " AND CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno,'')) LIKE ?";
            $params[] = "%" . $filtros['director'] . "%";
            $types .= "s";
        }

        if (!empty($filtros['estado']) && is_array($filtros['estado'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['estado']), '?'));
            $sql .= " AND t.estado IN ($placeholders)";

            foreach ($filtros['estado'] as $estado) {
                $params[] = $estado;
                $types .= "s";
            }
        }

        if (!empty($filtros['lies']) && is_array($filtros['lies'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['lies']), '?'));
            $sql .= " AND l.nombre IN ($placeholders)";

            foreach ($filtros['lies'] as $lies) {
                $params[] = $lies;
                $types .= "s";
            }
        }

        $sql .= " ORDER BY t.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}