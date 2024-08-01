<?php
header('Content-Type: application/json');
include_once '../Connection/Connection.php';

$con = Connection();
$response = [];
http_response_code(200);

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        $query = "SELECT * FROM dataservice";
        $result = $con->query($query);
        if ($result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $response['data'] = $data;
        } else {
            http_response_code(500);
            $response['error'] = "Error executing query: " . $con->error;
        }
        break;

    case 'POST':
        $inputData = file_get_contents("php://input");
        $data = json_decode($inputData, true);
        if (isset($data["name"]) && isset($data["description"])) {
            $name = $data["name"];
            $description = $data["description"];
            $query = "INSERT INTO dataservice (name, description) VALUES (?,?)";
            $st = $con->prepare($query);
            $st->bind_param("ss", $name, $description);
            if ($st->execute()) {
                $response['success'] = "Data was inserted.";
                $response['id'] = $con->insert_id;
            } else {
                http_response_code(500);
                $response['error'] = "Error executing query: " . $con->error;
            }
        } else {
            http_response_code(400);
            $response['error'] = "Invalid input data";
        }
        break;

    case 'PUT':
        $inputData = file_get_contents("php://input");
        $data = json_decode($inputData, true);
        if (isset($_GET["id"]) && isset($data["name"]) && isset($data["description"])) {
            $id = $_GET["id"];
            $name = $data["name"];
            $description = $data["description"];
            $query = "UPDATE dataservice SET name = ?, description = ? WHERE id = ?";
            $st = $con->prepare($query);
            $st->bind_param("ssi", $name, $description, $id);
            if ($st->execute()) {
                $response['success'] = "Data was updated.";
            } else {
                http_response_code(500);
                $response['error'] = "Error executing query: " . $con->error;
            }
        } else {
            http_response_code(400);
            $response['error'] = "Invalid input data";
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "DELETE FROM dataservice WHERE id = ?";
            $st = $con->prepare($query);
            $st->bind_param("i", $id);
            if ($st->execute()) {
                $response['success'] = "Record with ID $id was deleted.";
            } else {
                http_response_code(500);
                $response['error'] = "Error executing query: " . $con->error;
            }
        } else {
            http_response_code(400);
            $response['error'] = "ID parameter is required";
        }
        break;
    default:
        http_response_code(405);
        $response['error'] = 'Method not allowed';;
        break;
}
echo json_encode($response);
$con->close();
