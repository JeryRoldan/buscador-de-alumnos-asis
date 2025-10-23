<?php
$archivo = "Relacion_alumnos.csv";

if (!file_exists($archivo)) {
    file_put_contents($archivo, "N°,Código,DNI,Apellidos y Nombres,Sexo,Fecha Nac.,Edad,Tutor,Salón\n");
}

function leerCSV() {
    global $archivo;
    $data = [];
    if (($f = fopen($archivo, "r")) !== FALSE) {
        fgetcsv($f); // encabezado
        while (($row = fgetcsv($f)) !== FALSE) {
            $data[] = [
                "num" => $row[0],
                "codigo" => $row[1],
                "dni" => $row[2],
                "nombre" => $row[3],
                "sexo" => $row[4],
                "fechaNac" => $row[5],
                "edad" => $row[6],
                "tutor" => $row[7],
                "salon" => $row[8]
            ];
        }
        fclose($f);
    }
    return $data;
}

function guardarCSV($data) {
    global $archivo;
    $f = fopen($archivo, "w");
    fputcsv($f, ["N°","Código","DNI","Apellidos y Nombres","Sexo","Fecha Nac.","Edad","Tutor","Salón"]);
    foreach ($data as $i => $row) {
        fputcsv($f, [$i + 1, $row["codigo"], $row["dni"], $row["nombre"], $row["sexo"], $row["fechaNac"], $row["edad"], $row["tutor"], $row["salon"]]);
    }
    fclose($f);
}

if (isset($_GET["accion"]) && $_GET["accion"] == "listar") {
    header("Content-Type: application/json");
    echo json_encode(leerCSV());
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"];
    $alumnos = leerCSV();

    if ($accion === "agregar") {
        $nuevo = [
            "codigo" => $_POST["codigo"],
            "dni" => $_POST["dni"],
            "nombre" => $_POST["nombre"],
            "sexo" => $_POST["sexo"],
            "fechaNac" => $_POST["fechaNac"],
            "edad" => $_POST["edad"],
            "tutor" => $_POST["tutor"],
            "salon" => $_POST["salon"]
        ];
        $alumnos[] = $nuevo;
        guardarCSV($alumnos);
    }

    if ($accion === "eliminar") {
        $codigo = $_POST["codigo"];
        $filtrado = array_filter($alumnos, fn($a) => $a["codigo"] != $codigo);
        guardarCSV(array_values($filtrado));
    }
}
?>
