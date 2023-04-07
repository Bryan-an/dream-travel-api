<?php

namespace Src\Models;

class CustomerModel
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM clientes";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function get($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = ?";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(array($id));
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function insert(array $data)
    {
        $sql = "INSERT INTO clientes
                (nombre, apellido, correo, telefono)
            VALUES
                (:nombre, :apellido, :correo, :telefono)";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute(array(
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'telefono' => $data['telefono'],
            ));

            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE clientes
            SET
                nombre = :nombre,
                apellido = :apellido,
                correo = :correo,
                telefono = :telefono
            WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute(array(
                'id' => $id,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'telefono' => $data['telefono'],
            ));

            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM clientes WHERE id = ?";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(array($id));
            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }
}
