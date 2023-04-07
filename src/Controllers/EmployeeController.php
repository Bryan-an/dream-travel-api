<?php
namespace Src\Controllers;

use Src\Models\EmployeeModel;

class EmployeeController
{
    private $db;
    private $requestMethod;
    private $employeeId;
    private $employeeModel;

    public function __construct($db, $requestMethod, $employeeId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->employeeId = $employeeId;
        $this->employeeModel = new EmployeeModel($db);
    }

    public function processRequest()
    {
        if ($this->requestMethod === 'GET') {
            if ($this->employeeId) {
                $response = $this->getEmployee($this->employeeId);
            } else {
                $response = $this->getAllEmployees();
            }
        } else if ($this->requestMethod === 'POST') {
            $response = $this->createEmployee();
        } else if ($this->requestMethod === 'PUT') {
            $response = $this->updateEmployee($this->employeeId);
        } else if ($this->requestMethod === 'DELETE') {
            $response = $this->deleteEmployee($this->employeeId);
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllEmployees()
    {
        $result = $this->employeeModel->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getEmployee($id)
    {
        $result = $this->employeeModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createEmployee()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateEmployee($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->employeeModel->insert($data);
        $response['status_code_header'] = "HTTP/1.1 201 Created";
        $response['body'] = null;
        return $response;
    }

    private function updateEmployee($id)
    {
        $result = $this->employeeModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateEmployee($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->employeeModel->update($id, $data);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteEmployee($id)
    {
        $result = $this->employeeModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $this->employeeModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateEmployee($data)
    {
        if (!isset($data['nombre'])) {
            return "Nombre requerido";
        }

        if (!isset($data['apellido'])) {
            return "Apellido requerido";
        }

        if (!isset($data['correo'])) {
            return "Correo electrónico requerido";
        } else if (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $data['correo'])) {
            return "Correo electrónico inválido";
        }

        if (!isset($data['telefono'])) {
            return "Número de telefono requerido";
        } else if (!preg_match("/^\d+$/", $data['telefono'])) {
            return "Número de teléfono inválido";
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
