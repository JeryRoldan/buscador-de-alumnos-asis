<?php
// Configuración de conexión SQL Server
$server = "DESKTOP-6HNM4F3"; // Cambia por tu servidor
$database = "alumnosdb";

try {
    // Si estás en Windows con autenticación integrada
    $conn = new PDO("sqlsrv:Server=$server;Database=$database", "", "");
    
    // Para producción o Render, usa usuario y contraseña:
    // $conn = new PDO("sqlsrv:Server=$server;Database=$database", "usuario", "contraseña");

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a SQL Server: " . $e->getMessage());
}

// Listar alumnos
function listarAlumnos($conn) {
    $stmt = $conn->query("SELECT * FROM Alumnos ORDER BY id");
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear fecha para JSON
    foreach ($alumnos as &$row) {
        if (!empty($row['fechaNac'])) {
            $row['fechaNac'] = date('Y-m-d', strtotime($row['fechaNac']));
        }
    }

    return $alumnos;
}

if (isset($_GET["accion"]) && $_GET["accion"] === "listar") {
    header("Content-Type: application/json");
    echo json_encode(listarAlumnos($conn));
    exit;
}

// Agregar o eliminar alumno
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? '';

    if ($accion === "agregar") {
        $sql = "INSERT INTO Alumnos (codigo, dni, nombre, sexo, fechaNac, edad, tutor, salon)
                VALUES (:codigo, :dni, :nombre, :sexo, :fechaNac, :edad, :tutor, :salon)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':codigo' => $_POST["codigo"],
            ':dni' => $_POST["dni"],
            ':nombre' => $_POST["nombre"],
            ':sexo' => $_POST["sexo"],
            ':fechaNac' => $_POST["fechaNac"],
            ':edad' => $_POST["edad"],
            ':tutor' => $_POST["tutor"],
            ':salon' => $_POST["salon"]
        ]);
    }

    if ($accion === "eliminar") {
        $sql = "DELETE FROM Alumnos WHERE codigo = :codigo";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':codigo' => $_POST["codigo"]]);
    }
}
?>
