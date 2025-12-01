<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Alumno.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AlumnoController
{
    public $alumno;

    public function __construct($conn)
    {
        $this->alumno = new Alumno($conn);
    }

    // --- MÉTODOS CRUD ---
    public function index()
    {
        echo json_encode($this->alumno->getAll());
    }

    public function listarAlumnos()
    {
        echo json_encode($this->alumno->getAll());
    }

    public function paginated()
    {
        try {
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            // Validar parámetros
            if ($page < 1)
                $page = 1;
            if ($limit < 1 || $limit > 100)
                $limit = 10;

            $offset = ($page - 1) * $limit;

            // Obtener total de registros
            $total = $this->alumno->countAll($search);
            $totalPages = ceil($total / $limit);

            // Obtener alumnos paginados
            $alumnos = $this->alumno->getAllPaginated($offset, $limit, $search);

            echo json_encode([
                'success' => true,
                'alumnos' => $alumnos,
                'total' => $total,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit
            ]);

        } catch (Exception $e) {
            error_log("Error en paginated: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar los datos',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show()
    {
        if (isset($_GET['id']))
            echo json_encode($this->alumno->getById($_GET['id']));
        else
            echo json_encode(["error" => "ID no proporcionado"]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_alumno'])) {
            $id = $_POST['id_alumno'];
            $data = [
                'matricula' => $_POST['matricula'],
                'nombre' => $_POST['nombre'],
                'apellido_paterno' => $_POST['apellido_paterno'],
                'apellido_materno' => $_POST['apellido_materno'],
                'estatus' => 1, // Mantener activo al actualizar
                'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'],
                'carreras_id_carrera' => $_POST['carreras_id_carrera'],
                'grupos_id_grupo' => $_POST['grupos_id_grupo']
            ];
            if ($this->alumno->update($id, $data)) {
                echo json_encode(["success" => "Alumno actualizado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar el alumno"]);
            }
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'matricula' => $_POST['matricula'],
                'nombre' => $_POST['nombre'],
                'apellido_paterno' => $_POST['apellido_paterno'],
                'apellido_materno' => $_POST['apellido_materno'],
                'estatus' => 1, // Siempre activo al crear
                'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'],
                'carreras_id_carrera' => $_POST['carreras_id_carrera'],
                'grupos_id_grupo' => $_POST['grupos_id_grupo']
            ];
            if ($this->alumno->create($data)) {
                echo json_encode(["success" => "Alumno creado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al crear el alumno"]);
            }
        } else {
            echo json_encode(["error" => "Método no permitido"]);
        }
    }

    public function delete()
    {
        // Es mejor delegar la lógica de la consulta al modelo.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            if ($this->alumno->delete($_POST['id'])) {
                echo json_encode(["success" => "Alumno eliminado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al eliminar el alumno"]);
            }
        } else {
            echo json_encode(["error" => "Datos incompletos"]);
        }
    }

    // --- MÉTODOS DE PAGINACIÓN Y BÚSQUEDA ---
    public function contarTotalCarreras($terminoBusqueda)
    {
        $sql = "SELECT COUNT(DISTINCT c.id_carrera) 
                FROM carreras c
                LEFT JOIN alumnos a ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON g.carreras_id_carrera = c.id_carrera
                WHERE (LOWER(c.nombre) LIKE LOWER(:termino) OR LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerCarrerasPaginadas($terminoBusqueda, $offset, $limit)
    {
        $sql = "SELECT DISTINCT c.* FROM carreras c
                LEFT JOIN alumnos a ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON g.carreras_id_carrera = c.id_carrera
                WHERE (LOWER(c.nombre) LIKE LOWER(:termino) OR LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))
                ORDER BY c.nombre LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarTotalGruposPorCarrera($idCarrera, $terminoBusqueda)
    {
        $sql = "SELECT COUNT(DISTINCT g.id_grupo) FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.carreras_id_carrera = :idCarrera AND (LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function contarTotalAlumnosPorGrupo($id_grupo, $terminoBusqueda = '')
    {
        $sql = "SELECT COUNT(id_alumno) FROM alumnos WHERE grupos_id_grupo = :id_grupo";
        if (!empty($terminoBusqueda)) {
            $sql .= " AND (LOWER(nombre) LIKE LOWER(:termino) OR LOWER(apellido_paterno) LIKE LOWER(:termino) OR LOWER(apellido_materno) LIKE LOWER(:termino) OR LOWER(matricula) LIKE LOWER(:termino))";
        }
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        if (!empty($terminoBusqueda)) {
            $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerAlumnosPaginadosPorGrupo($id_grupo, $offset, $limit, $terminoBusqueda = '')
    {
        $sql = "SELECT * FROM alumnos WHERE grupos_id_grupo = :id_grupo";
        if (!empty($terminoBusqueda)) {
            $sql .= " AND (LOWER(nombre) LIKE LOWER(:termino) OR LOWER(apellido_paterno) LIKE LOWER(:termino) OR LOWER(apellido_materno) LIKE LOWER(:termino) OR LOWER(matricula) LIKE LOWER(:termino))";
        }
        $sql .= " ORDER BY apellido_paterno, apellido_materno, nombre LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        if (!empty($terminoBusqueda)) {
            $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerGruposPaginadosPorCarrera($idCarrera, $terminoBusqueda, $offset, $limit)
    {
        $sql = "SELECT DISTINCT g.id_grupo, g.nombre FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.carreras_id_carrera = :idCarrera AND (LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))
                ORDER BY g.nombre LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarTotalGruposPorTutor($idUsuario, $terminoBusqueda)
    {
        $sql = "SELECT COUNT(DISTINCT g.id_grupo) FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :idUsuario AND (LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerGruposPaginadosPorTutor($idUsuario, $terminoBusqueda, $offset, $limit)
    {
        $sql = "SELECT DISTINCT g.id_grupo, g.nombre FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :idUsuario AND (LOWER(g.nombre) LIKE LOWER(:termino) OR LOWER(a.nombre) LIKE LOWER(:termino) OR LOWER(a.apellido_paterno) LIKE LOWER(:termino) OR LOWER(a.apellido_materno) LIKE LOWER(:termino) OR LOWER(a.matricula) LIKE LOWER(:termino))
                ORDER BY g.nombre LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function renderizarListaAlumnosPaginados($id_grupo, $pagina)
    {
        $alumnosPorPagina = 5;
        $offset = ($pagina - 1) * $alumnosPorPagina;
        $alumnos = $this->obtenerAlumnosPaginadosPorGrupo($id_grupo, $offset, $alumnosPorPagina);

        ob_start();
        if (empty($alumnos)) {
            echo '<div class="alert alert-secondary py-2 mb-0"><i class="bi bi-info-circle me-1"></i> No hay más alumnos en esta página.</div>';
        } else {
            echo '<ul class="list-group list-group-flush">';
            foreach ($alumnos as $a) {
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person-circle text-muted fs-5 me-2"></i>
                        <span><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido_paterno'] . ' ' . ($a['apellido_materno'] ?? '')) ?></span>
                    </div>
                    <div class="btn-group" role="group" aria-label="Acciones de alumno">
                        <a href="crear_seguimiento.php?id_alumno=<?= htmlspecialchars($a['id_alumno']) ?>"
                            class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Crear nuevo seguimiento"><i
                                class="bi bi-journal-plus"></i></a>
                        <a href="ver_seguimientos.php?id_alumno=<?= htmlspecialchars($a['id_alumno']) ?>"
                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver seguimientos del alumno"><i
                                class="bi bi-card-list"></i></a>
                    </div>
                </li>
                <?php
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }

    /**
     * [REFACTORIZADO] Método que renderiza la lista de alumnos para un conjunto de grupos.
     * Ahora obtiene todos los datos necesarios en pocas consultas antes de empezar a renderizar.
     */
    public function listarAlumnosPorIdsDeGrupos($grupos_ids, $conn, $modo = false, string $parentUid = "root", $terminoBusqueda = ''): bool|string
    {
        if (empty($grupos_ids)) {
            return '';
        }

        ob_start();
        $raw_group_ids = array_column($grupos_ids, 'id_grupo');
        $alumnosPorPagina = 5;

        $placeholders = implode(',', array_fill(0, count($raw_group_ids), '?'));
        $sql_info = "SELECT g.id_grupo, g.nombre AS nombre_grupo, c.nombre AS nombre_carrera
                     FROM grupos g
                     JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                     WHERE g.id_grupo IN ($placeholders)";
        $stmt_info = $conn->prepare($sql_info);
        $stmt_info->execute($raw_group_ids);
        $info_grupos = [];
        while ($row = $stmt_info->fetch(PDO::FETCH_ASSOC)) {
            $info_grupos[$row['id_grupo']] = $row;
        }

        $alumnos_todos = $this->alumno->listByGroupIds($raw_group_ids, $terminoBusqueda);

        // Agrupamos los alumnos por su ID de grupo en un array de PHP.
        $alumnos_por_grupo = [];
        foreach ($alumnos_todos as $alumno) {
            $alumnos_por_grupo[$alumno['grupos_id_grupo']][] = $alumno;
        }

        // (Opcional, solo para modo acordeón) PASO 2.B: Contar alumnos por grupo en UNA SOLA CONSULTA.
        $totalAlumnosPorGrupo = [];
        if ($modo === false) {
            $sql_counts = "SELECT grupos_id_grupo, COUNT(id_alumno) as total 
                            FROM alumnos 
                            WHERE grupos_id_grupo IN ($placeholders)
                            GROUP BY grupos_id_grupo";
            $stmt_counts = $conn->prepare($sql_counts);
            $stmt_counts->execute($raw_group_ids);
            while ($row = $stmt_counts->fetch(PDO::FETCH_ASSOC)) {
                $totalAlumnosPorGrupo[$row['grupos_id_grupo']] = $row['total'];
            }
        }

        // PASO 3: Iterar y renderizar el HTML SIN hacer más consultas a la base de datos.
        if ($modo === true): ?>
            <ul class="list-group shadow-sm rounded-3 mb-3">
                <?php foreach ($raw_group_ids as $id_grupo):
                    $info = $info_grupos[$id_grupo] ?? null;
                    if (!$info)
                        continue;

                    $grupo_nombre = $info['nombre_grupo'];
                    $carrera_nombre = $info['nombre_carrera'];
                    $alumnos = array_slice($alumnos_por_grupo[$id_grupo] ?? [], 0, $alumnosPorPagina);

                    if (!empty($alumnos)):
                        foreach ($alumnos as $a): ?>
                            <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3">
                                <div>
                                    <strong
                                        class="d-block text-dark fs-5"><?= htmlspecialchars("{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}") ?></strong>
                                    <small class="text-muted">
                                        <i class="bi bi-mortarboard" title="Carrera"></i> <?= htmlspecialchars($carrera_nombre) ?>
                                        <span class="mx-2 text-secondary">|</span>
                                        <i class="bi bi-people" title="Grupo"></i> <?= htmlspecialchars($grupo_nombre) ?>
                                    </small>
                                </div>
                                <div class="btn-group mt-2 mt-md-0" role="group">
                                    <a href="crear_seguimiento.php?id_alumno=<?= $a['id_alumno'] ?>" class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="tooltip" title="Crear seguimiento"><i class="bi bi-journal-plus"></i></a>
                                    <a href="ver_seguimientos.php?id_alumno=<?= $a['id_alumno'] ?>" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="tooltip" title="Ver seguimientos"><i class="bi bi-card-list"></i></a>
                                </div>
                            </li>
                        <?php endforeach;
                    endif;
                endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="accordion" id="accordion_<?= htmlspecialchars($parentUid) ?>">
                <?php foreach ($raw_group_ids as $id_grupo):
                    $info = $info_grupos[$id_grupo] ?? null;
                    if (!$info)
                        continue;

                    $grupo_nombre = $info['nombre_grupo'];
                    $alumnos = array_slice($alumnos_por_grupo[$id_grupo] ?? [], 0, $alumnosPorPagina);
                    $totalAlumnos = $totalAlumnosPorGrupo[$id_grupo] ?? 0;
                    $totalPagesAlumnos = ceil($totalAlumnos / $alumnosPorPagina);
                    $grupoUid = "grupo_" . $id_grupo . "_" . uniqid();
                    ?>
                    <div class="accordion-item shadow-sm rounded-3 mb-2 border-0">
                        <h2 class="accordion-header" id="heading_<?= htmlspecialchars($grupoUid) ?>">
                            <button class="accordion-button collapsed bg-light fw-bold small" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse_<?= htmlspecialchars($grupoUid) ?>">
                                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center w-100 me-2">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <i class="bi bi-people-fill me-2 text-primary"></i>
                                        <span class="text-muted small">Grupo: <?= htmlspecialchars($grupo_nombre) ?></span>
                                    </div>
                                    <span class="badge bg-primary ms-md-2 mt-1 mt-md-0"><?= $totalAlumnos ?> alumnos</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse_<?= htmlspecialchars($grupoUid) ?>" class="accordion-collapse collapse"
                            data-bs-parent="#accordion_<?= htmlspecialchars($parentUid) ?>">
                            <div class="accordion-body p-2 p-md-3">
                                <?php if ($totalAlumnos > 0): ?>
                                <div class="d-grid d-md-flex gap-2 mb-3">
                                    <a href="gestionar_listas.php?id_grupo=<?= htmlspecialchars($id_grupo) ?>"
                                        class="btn btn-outline-primary btn-sm flex-fill flex-md-none" data-bs-toggle="tooltip" title="Gestionar Listas">
                                        <i class="bi bi-pencil-square me-1 d-md-none"></i>
                                        <span class="d-none d-md-inline"><i class="bi bi-pencil-square"></i></span>
                                        <span class="d-md-none">Gestionar</span>
                                    </a>
                                    <button type="button" class="btn btn-outline-success btn-sm btn-tutoria-grupal flex-fill flex-md-none"
                                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                        data-grupo-nombre="<?= htmlspecialchars($grupo_nombre) ?>" data-bs-toggle="tooltip"
                                        title="Tomar Lista Grupal">
                                        <i class="bi bi-people-fill me-1"></i>
                                        <span class="d-none d-md-inline">Grupal</span>
                                        <span class="d-md-none">Tomar Lista Grupal</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm btn-tutoria-individual flex-fill flex-md-none"
                                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                        data-grupo-nombre="<?= htmlspecialchars($grupo_nombre) ?>" data-bs-toggle="tooltip"
                                        title="Tomar Lista Individual">
                                        <i class="bi bi-person-fill me-1"></i>
                                        <span class="d-none d-md-inline">Individual</span>
                                        <span class="d-md-none">Tomar Lista Individual</span>
                                    </button>
                                    <a href="ver-alumnos-grupo?id_grupo=<?= htmlspecialchars($id_grupo) ?>"
                                        class="btn btn-outline-secondary btn-sm flex-fill flex-md-none" data-bs-toggle="tooltip" title="Ver Alumnos del Grupo">
                                        <i class="bi bi-gear-fill me-1 d-md-none"></i>
                                        <span class="d-none d-md-inline"><i class="bi bi-gear-fill"></i></span>
                                        <span class="d-md-none">Alumnos</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div id="lista-alumnos-<?= htmlspecialchars($id_grupo) ?>">
                                    <?php if (empty($alumnos)): ?>
                                        <div class="alert alert-secondary py-2 mb-0"><i class="bi bi-info-circle me-1"></i> No hay alumnos
                                            en este grupo.</div>
                                    <?php else: ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($alumnos as $a): ?>
                                                <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 p-2 p-md-3">
                                                    <div class="d-flex align-items-center flex-grow-1 w-100 w-md-auto">
                                                        <i class="bi bi-person-circle text-muted fs-5 me-2 d-none d-md-inline"></i>
                                                        <i class="bi bi-person-circle text-muted me-2 d-md-none"></i>
                                                        <span class="nombre-alumno-mobile small-md"><?= htmlspecialchars("{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}") ?></span>
                                                    </div>
                                                    <div class="btn-group btn-group-mobile w-100 w-md-auto" role="group">
                                                        <a href="crear_seguimiento.php?id_alumno=<?= $a['id_alumno'] ?>"
                                                            class="btn btn-sm btn-outline-success flex-fill flex-md-none" data-bs-toggle="tooltip"
                                                            title="Crear seguimiento">
                                                            <i class="bi bi-journal-plus me-1 d-md-none"></i>
                                                            <span class="d-none d-md-inline"><i class="bi bi-journal-plus"></i></span>
                                                            <span class="d-md-none">Crear seguimiento</span>
                                                        </a>
                                                        <a href="ver_seguimientos.php?id_alumno=<?= $a['id_alumno'] ?>"
                                                            class="btn btn-sm btn-outline-primary flex-fill flex-md-none" data-bs-toggle="tooltip"
                                                            title="Ver seguimientos">
                                                            <i class="bi bi-card-list me-1 d-md-none"></i>
                                                            <span class="d-none d-md-inline"><i class="bi bi-card-list"></i></span>
                                                            <span class="d-md-none">Ver seguimiento</span>
                                                        </a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                <?php if ($totalPagesAlumnos > 1): ?>
                                    <nav aria-label="Paginación de alumnos" class="mt-3">
                                        <ul class="pagination pagination-sm justify-content-end" data-id-grupo="<?= $id_grupo ?>"
                                            data-total-pages="<?= $totalPagesAlumnos ?>" data-current-page="1">
                                            <li class="page-item disabled" data-role="prev"><a class="page-link" href="#">&laquo;</a></li>
                                            <li class="page-item active" data-role="page-indicator"><span class="page-link">1 de
                                                    <?= $totalPagesAlumnos ?></span></li>
                                            <li class="page-item" data-role="next"><a class="page-link" href="#">&raquo;</a></li>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif;
        return ob_get_clean();
    }

    // --- Resto de la clase sin cambios ---

    public function renderizarAcordeonCarrera($dataCarrera, $conn, $auth, $modo = false, $terminoBusqueda = '')
    {
        ob_start();

        $carreraid = $dataCarrera["id_carrera"];
        $nombre_carrera = $dataCarrera["nombre"];
        $carreraUid = "carrera_" . $carreraid;

        // Esta consulta ahora solo se usa para obtener la lista de IDs, es eficiente.
        $grupos_ids = $auth->usuario->getGruposIdByCarreraIdFiltered($carreraid, $terminoBusqueda);

        if ($modo === true) {
            if (!empty($grupos_ids)) {
                // La llamada a la función refactorizada se encarga del resto eficientemente.
                echo $this->listarAlumnosPorIdsDeGrupos($grupos_ids, $conn, true, $carreraUid, $terminoBusqueda);
            }
        } else {
            ?>
            <div class="accordion mb-3" id="<?= $carreraUid ?>">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_<?= $carreraUid ?>">
                        <button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse_<?= $carreraUid ?>">
                            <i class="bi bi-mortarboard-fill me-2"></i> 
                            <span class="text-truncate">Carrera: <?= htmlspecialchars($nombre_carrera) ?></span>
                        </button>
                    </h2>
                    <div id="collapse_<?= $carreraUid ?>" class="accordion-collapse collapse" data-bs-parent="#<?= $carreraUid ?>">
                        <div class="accordion-body">
                            <?php
                            if (!empty($grupos_ids)) {
                                echo $this->listarAlumnosPorIdsDeGrupos($grupos_ids, $conn, false, $carreraUid, $terminoBusqueda);
                            } else {
                                echo "<p>No se encontraron grupos o alumnos que coincidan con la búsqueda.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    public function getNombreGrupo($id_grupo)
    {
        $sql = "SELECT nombre FROM grupos WHERE id_grupo = :id_grupo";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nombre'] : null;
    }

    public function getAlumnosByGrupo($id_grupo)
    {
        $query = "SELECT a.id_alumno, a.matricula, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as nombre_completo, g.nombre as nombre_grupo
                  FROM alumnos a
                  JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                  WHERE a.grupos_id_grupo = :id_grupo
                  ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";
        $stmt = $this->alumno->conn->prepare($query);
        $stmt->bindParam(":id_grupo", $id_grupo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAlumnoPorId($idAlumno)
    {
        $sql = "SELECT * FROM alumnos WHERE id_alumno = :id_alumno";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':id_alumno', $idAlumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new AlumnoController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>