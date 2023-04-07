<?php
namespace Src\Controllers;

use Src\Models\TravelModel;

class TravelController
{
    private $db;
    private $requestMethod;
    private $travelId;
    private $travelModel;

    public function __construct($db, $requestMethod, $travelId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->travelId = $travelId;
        $this->travelModel = new TravelModel($db);
    }

    public function processRequest()
    {
        if ($this->requestMethod === 'GET') {
            if ($this->travelId) {
                $response = $this->getTravel($this->travelId);
            } else {
                $response = $this->getAllTravels();
            }
        } else if ($this->requestMethod === 'POST') {
            $response = $this->createTravel();
        } else if ($this->requestMethod === 'PUT') {
            $response = $this->updateTravel($this->travelId);
        } else if ($this->requestMethod === 'DELETE') {
            $response = $this->deleteTravel($this->travelId);
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllTravels()
    {
        $result = $this->travelModel->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getTravel($id)
    {
        $result = $this->travelModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createTravel()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateTravel($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->travelModel->insert($data);
        $response['status_code_header'] = "HTTP/1.1 201 Created";
        $response['body'] = null;
        return $response;
    }

    private function updateTravel($id)
    {
        $result = $this->travelModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateTravel($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->travelModel->update($id, $data);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteTravel($id)
    {
        $result = $this->travelModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $this->travelModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateTravel($data)
    {
        if (!isset($data['destino'])) {
            return "Destino requerido";
        }

        if (!isset($data['fecha_salida'])) {
            return "Fecha de salida requerida";
        }

        if (!isset($data['fecha_regreso'])) {
            return "Fecha de regreso requerida";
        }

        if (!isset($data['precio'])) {
            return "Precio requerido";
        } else if (!preg_match("/^\d+(\.\d{1,2})?$/", $data['precio'])) {
            return "Precio inválido";
        }

        if (!isset($data['id_cliente'])) {
            return "Cliente requerido";
        }

        if (!isset($data['id_empleado'])) {
            return "Empleado requerido";
        }

        return false;
    }

    private function badRequestResponse($error)
    {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';

        $response['body'] = json_encode([
            'error' => $error,
        ]);

        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
