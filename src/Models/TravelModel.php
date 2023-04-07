<?php

namespace Src\Models;

class TravelModel
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM viajes";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function get($id)
    {
        $sql = "SELECT * FROM viajes WHERE id = ?";

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
        $sql = "INSERT INTO viajes
                (destino, fecha_salida, fecha_regreso, precio, id_cliente, id_empleado)
            VALUES
                (:destino, :fecha_salida, :fecha_regreso, :precio, :id_cliente, :id_empleado)";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute(array(
                'destino' => $data['destino'],
                'fecha_salida' => $data['fecha_salida'],
                'fecha_regreso' => $data['fecha_regreso'],
                'precio' => $data['precio'],
                'id_cliente' => $data['id_cliente'],
                'id_empleado' => $data['id_empleado'],
            ));

            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE viajes
            SET
                destino = :destino,
                fecha_salida = :fecha_salida,
                fecha_regreso = :fecha_regreso,
                precio = :precio,
                id_cliente = :id_cliente,
                id_empleado = :id_empleado
            WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute(array(
                'id' => $id,
                'destino' => $data['destino'],
                'fecha_salida' => $data['fecha_salida'],
                'fecha_regreso' => $data['fecha_regreso'],
                'precio' => $data['precio'],
                'id_cliente' => $data['id_cliente'],
                'id_empleado' => $data['id_empleado'],
            ));

            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM viajes WHERE id = ?";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(array($id));
            return $statement->rowCount();
        } catch (\PDOException$e) {
            exit($e->getMessage());
        }
    }
}
