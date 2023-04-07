<?php
namespace Src\Controllers;

use Src\Models\CustomerModel;

class CustomerController
{
    private $db;
    private $requestMethod;
    private $customerId;
    private $customerModel;

    public function __construct($db, $requestMethod, $customerId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->customerId = $customerId;
        $this->customerModel = new CustomerModel($db);
    }

    public function processRequest()
    {
        if ($this->requestMethod === 'GET') {
            if ($this->customerId) {
                $response = $this->getCustomer($this->customerId);
            } else {
                $response = $this->getAllCustomers();
            }
        } else if ($this->requestMethod === 'POST') {
            $response = $this->createCustomer();
        } else if ($this->requestMethod === 'PUT') {
            $response = $this->updateCustomer($this->customerId);
        } else if ($this->requestMethod === 'DELETE') {
            $response = $this->deleteCustomer($this->customerId);
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllCustomers()
    {
        $result = $this->customerModel->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getCustomer($id)
    {
        $result = $this->customerModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createCustomer()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateCustomer($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->customerModel->insert($data);
        $response['status_code_header'] = "HTTP/1.1 201 Created";
        $response['body'] = null;
        return $response;
    }

    private function updateCustomer($id)
    {
        $result = $this->customerModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $data = (array) json_decode(file_get_contents("php://input"), true);
        $error = $this->validateCustomer($data);

        if ($error) {
            return $this->badRequestResponse($error);
        }

        $this->customerModel->update($id, $data);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteCustomer($id)
    {
        $result = $this->customerModel->get($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $this->customerModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateCustomer($data)
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
