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
        $query = trim($query);

        if ($query === '') {

            $sql = "
                SELECT 
                    t.id_tesis,
                    t.titulo,
                    YEAR(t.fecha_registro) AS anio,
                    CONCAT('/repositorio_MIIDT', t.portada) AS imagen,
                    t.estado,

                    TRIM(CONCAT(
                        IFNULL(a.nombre, ''),
                        ' ',
                        IFNULL(a.apellido_paterno, ''),
                        ' ',
                        IFNULL(a.apellido_materno, '')
                    )) AS autor,

                    TRIM(CONCAT(
                        IFNULL(d.nombre, ''),
                        ' ',
                        IFNULL(d.apellido_paterno, ''),
                        ' ',
                        IFNULL(d.apellido_materno, '')
                    )) AS director,

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

                TRIM(CONCAT(
                    IFNULL(a.nombre, ''),
                    ' ',
                    IFNULL(a.apellido_paterno, ''),
                    ' ',
                    IFNULL(a.apellido_materno, '')
                )) AS autor,

                TRIM(CONCAT(
                    IFNULL(d.nombre, ''),
                    ' ',
                    IFNULL(d.apellido_paterno, ''),
                    ' ',
                    IFNULL(d.apellido_materno, '')
                )) AS director,

                l.nombre AS lies

            FROM tesis t
            LEFT JOIN autor a ON a.matricula = t.matricula
            LEFT JOIN director d ON d.id_director = t.id_director
            LEFT JOIN linea_investigacion l ON l.id_linea = a.id_linea

            WHERE 
                t.titulo LIKE ?
                OR TRIM(CONCAT(
                    IFNULL(a.nombre, ''),
                    ' ',
                    IFNULL(a.apellido_paterno, ''),
                    ' ',
                    IFNULL(a.apellido_materno, '')
                )) LIKE ?
                OR TRIM(CONCAT(
                    IFNULL(d.nombre, ''),
                    ' ',
                    IFNULL(d.apellido_paterno, ''),
                    ' ',
                    IFNULL(d.apellido_materno, '')
                )) LIKE ?

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

                TRIM(CONCAT(
                    IFNULL(a.nombre, ''),
                    ' ',
                    IFNULL(a.apellido_paterno, ''),
                    ' ',
                    IFNULL(a.apellido_materno, '')
                )) AS autor,

                TRIM(CONCAT(
                    IFNULL(d.nombre, ''),
                    ' ',
                    IFNULL(d.apellido_paterno, ''),
                    ' ',
                    IFNULL(d.apellido_materno, '')
                )) AS director,

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
            $sql .= "
                AND TRIM(CONCAT(
                    IFNULL(d.nombre, ''),
                    ' ',
                    IFNULL(d.apellido_paterno, ''),
                    ' ',
                    IFNULL(d.apellido_materno, '')
                )) LIKE ?
            ";
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
                TRIM(CONCAT(
                    IFNULL(nombre, ''),
                    ' ',
                    IFNULL(apellido_paterno, ''),
                    ' ',
                    IFNULL(apellido_materno, '')
                )) AS nombre_completo
            FROM director
            WHERE TRIM(CONCAT(
                IFNULL(nombre, ''),
                ' ',
                IFNULL(apellido_paterno, ''),
                ' ',
                IFNULL(apellido_materno, '')
            )) LIKE ?
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

    public function obtenerOCrearDirector(string $nombreCompleto, int $idLinea): int
    {
        $nombreCompleto = $this->limpiarEspacios($nombreCompleto);

        if ($nombreCompleto === '') {
            throw new RuntimeException('Escribe el nombre del director de tesis.');
        }

        if ($idLinea <= 0) {
            throw new RuntimeException('Selecciona una línea de investigación para registrar el director.');
        }

        $directorExistente = $this->buscarDirectorExactoPorNombre($nombreCompleto);

        if ($directorExistente > 0) {
            return $directorExistente;
        }

        $partes = $this->separarNombreDirector($nombreCompleto);

        $sql = "
            INSERT INTO director
                (
                    id_linea,
                    nombre,
                    apellido_paterno,
                    apellido_materno,
                    fecha_nacimiento
                )
            VALUES
                (?, ?, ?, ?, NULL)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('No se pudo preparar el registro del director.');
        }

        $stmt->bind_param(
            'isss',
            $idLinea,
            $partes['nombre'],
            $partes['apellido_paterno'],
            $partes['apellido_materno']
        );

        if (!$stmt->execute()) {
            throw new RuntimeException('No se pudo registrar el director de tesis.');
        }

        return (int)$this->db->insert_id;
    }

    private function buscarDirectorExactoPorNombre(string $nombreCompleto): int
    {
        $nombreCompleto = $this->limpiarEspacios($nombreCompleto);

        $sql = "
            SELECT id_director
            FROM director
            WHERE LOWER(TRIM(CONCAT(
                IFNULL(nombre, ''),
                ' ',
                IFNULL(apellido_paterno, ''),
                ' ',
                IFNULL(apellido_materno, '')
            ))) = LOWER(?)
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('s', $nombreCompleto);
        $stmt->execute();

        $result = $stmt->get_result();
        $director = $result->fetch_assoc();

        return $director ? (int)$director['id_director'] : 0;
    }

    private function separarNombreDirector(string $nombreCompleto): array
    {
        $nombreCompleto = $this->limpiarEspacios($nombreCompleto);
        $partes = explode(' ', $nombreCompleto);
        $total = count($partes);

        if ($total < 2) {
            throw new RuntimeException('Escribe al menos nombre y apellido del director.');
        }

        if ($total === 2) {
            return [
                'nombre' => $partes[0],
                'apellido_paterno' => $partes[1],
                'apellido_materno' => null
            ];
        }

        $apellidoMaterno = array_pop($partes);
        $apellidoPaterno = array_pop($partes);
        $nombre = implode(' ', $partes);

        return [
            'nombre' => $nombre,
            'apellido_paterno' => $apellidoPaterno,
            'apellido_materno' => $apellidoMaterno
        ];
    }

    private function limpiarEspacios(string $texto): string
    {
        return trim((string)preg_replace('/\s+/', ' ', $texto));
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

    public function obtenerPorId(int $idTesis): ?array
    {
        $sql = "
            SELECT
                t.id_tesis,
                t.titulo,
                t.url,
                t.portada,
                t.estado,
                DATE_FORMAT(t.fecha_registro, '%d/%m/%Y') AS fecha_tesis,

                a.matricula,
                a.id_linea,
                a.nombre AS autor_nombre,
                a.apellido_paterno AS autor_apellido_paterno,
                a.apellido_materno AS autor_apellido_materno,
                a.correo_institucional AS autor_correo,
                a.sexo AS autor_sexo,

                TRIM(CONCAT(
                    IFNULL(a.nombre, ''),
                    ' ',
                    IFNULL(a.apellido_paterno, ''),
                    ' ',
                    IFNULL(a.apellido_materno, '')
                )) AS autor_completo,

                d.id_director,
                d.id_linea AS director_id_linea,
                d.nombre AS director_nombre,
                d.apellido_paterno AS director_apellido_paterno,
                d.apellido_materno AS director_apellido_materno,

                TRIM(CONCAT(
                    IFNULL(d.nombre, ''),
                    ' ',
                    IFNULL(d.apellido_paterno, ''),
                    ' ',
                    IFNULL(d.apellido_materno, '')
                )) AS director_completo,

                l.nombre AS linea_nombre

            FROM tesis t
            LEFT JOIN autor a ON a.matricula = t.matricula
            LEFT JOIN director d ON d.id_director = t.id_director
            LEFT JOIN linea_investigacion l ON l.id_linea = a.id_linea

            WHERE t.id_tesis = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $idTesis);
        $stmt->execute();

        $result = $stmt->get_result();
        $tesis = $result->fetch_assoc();

        return $tesis ?: null;
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

    public function actualizarTesis(array $data): void
    {
        $this->db->begin_transaction();

        try {
            $autor = $data['autor'];
            $tesis = $data['tesis'];

            $this->guardarAutor($autor);

            if (!empty($tesis['portada'])) {
                $sqlTesis = "
                    UPDATE tesis
                    SET 
                        matricula = ?,
                        id_director = ?,
                        titulo = ?,
                        url = ?,
                        portada = ?,
                        fecha_registro = ?,
                        estado = ?
                    WHERE id_tesis = ?
                    LIMIT 1
                ";

                $stmt = $this->db->prepare($sqlTesis);

                if (!$stmt) {
                    throw new RuntimeException('No se pudo preparar la actualización de la tesis.');
                }

                $stmt->bind_param(
                    'sisssssi',
                    $tesis['matricula'],
                    $tesis['id_director'],
                    $tesis['titulo'],
                    $tesis['url'],
                    $tesis['portada'],
                    $tesis['fecha_registro'],
                    $tesis['estado'],
                    $tesis['id_tesis']
                );
            } else {
                $sqlTesis = "
                    UPDATE tesis
                    SET 
                        matricula = ?,
                        id_director = ?,
                        titulo = ?,
                        url = ?,
                        fecha_registro = ?,
                        estado = ?
                    WHERE id_tesis = ?
                    LIMIT 1
                ";

                $stmt = $this->db->prepare($sqlTesis);

                if (!$stmt) {
                    throw new RuntimeException('No se pudo preparar la actualización de la tesis.');
                }

                $stmt->bind_param(
                    'sissssi',
                    $tesis['matricula'],
                    $tesis['id_director'],
                    $tesis['titulo'],
                    $tesis['url'],
                    $tesis['fecha_registro'],
                    $tesis['estado'],
                    $tesis['id_tesis']
                );
            }

            if (!$stmt->execute()) {
                throw new RuntimeException('No se pudo actualizar la tesis.');
            }

            $this->db->commit();

        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function eliminar(int $idTesis): void
    {
        $this->db->begin_transaction();

        try {
            $sql = "
                DELETE FROM tesis
                WHERE id_tesis = ?
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                throw new RuntimeException('No se pudo preparar la eliminación de la tesis.');
            }

            $stmt->bind_param('i', $idTesis);

            if (!$stmt->execute()) {
                throw new RuntimeException('No se pudo eliminar la tesis.');
            }

            if ($stmt->affected_rows <= 0) {
                throw new RuntimeException('No se encontró la tesis que deseas eliminar.');
            }

            $this->db->commit();

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