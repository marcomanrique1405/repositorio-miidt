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

    public function buscarDirectores(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $sql = "
            SELECT 
                id_director,
                id_linea,
                nombre,
                apellido_paterno,
                apellido_materno,
                CONCAT_WS(' ', nombre, apellido_paterno, apellido_materno) AS nombre_completo
            FROM director
            WHERE CONCAT_WS(' ', nombre, apellido_paterno, apellido_materno) LIKE ?
            ORDER BY nombre ASC, apellido_paterno ASC
            LIMIT 10
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $like = '%' . $query . '%';

        $stmt->bind_param('s', $like);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function directorPerteneceALinea(int $idDirector, int $idLinea): bool
    {
        $sql = "
            SELECT id_director
            FROM director
            WHERE id_director = ?
            AND id_linea = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ii', $idDirector, $idLinea);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function guardarAlta(array $data): int
    {
        $this->db->begin_transaction();

        try {
            $autor = $data['autor'];
            $tesis = $data['tesis'];

            $this->guardarAutor($autor);

            $sqlTesis = "
                INSERT INTO tesis
                    (matricula, id_director, titulo, url, portada, fecha_registro, estado)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $this->db->prepare($sqlTesis);

            if (!$stmt) {
                throw new RuntimeException('No se pudo preparar el registro de la tesis.');
            }

            $stmt->bind_param(
                'sisssss',
                $tesis['matricula'],
                $tesis['id_director'],
                $tesis['titulo'],
                $tesis['url'],
                $tesis['portada'],
                $tesis['fecha_registro'],
                $tesis['estado']
            );

            if (!$stmt->execute()) {
                throw new RuntimeException('No se pudo guardar la tesis.');
            }

            $idTesis = (int)$this->db->insert_id;

            $this->db->commit();

            return $idTesis;

        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function guardarAutor(array $autor): void
    {
        $sql = "
            INSERT INTO autor
                (
                    matricula,
                    id_linea,
                    nombre,
                    apellido_paterno,
                    apellido_materno,
                    correo_institucional,
                    sexo
                )
            VALUES
                (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                id_linea = VALUES(id_linea),
                nombre = VALUES(nombre),
                apellido_paterno = VALUES(apellido_paterno),
                apellido_materno = VALUES(apellido_materno),
                correo_institucional = VALUES(correo_institucional),
                sexo = VALUES(sexo)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('No se pudo preparar el registro del autor.');
        }

        $stmt->bind_param(
            'sisssss',
            $autor['matricula'],
            $autor['id_linea'],
            $autor['nombre'],
            $autor['apellido_paterno'],
            $autor['apellido_materno'],
            $autor['correo_institucional'],
            $autor['sexo']
        );

        if (!$stmt->execute()) {
            throw new RuntimeException('No se pudo guardar el autor.');
        }
    }
}