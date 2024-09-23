<?php

require_once 'Database.php';

class CRUD {
    private $conn;
    private $table_name;

    public function __construct($table_name) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->table_name = $table_name;
    }

    // Create Operation
    public function create($data) {
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));

            $sql = "INSERT INTO " . $this->table_name . " ($columns) VALUES ($placeholders)";
            $stmt = $this->conn->prepare($sql);

            // Bind values dynamically
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
            }

            return $stmt->execute();
        } catch(PDOException $exception) {
            echo "Create error: " . $exception->getMessage();
            return false;
        }
    }

    // Read Operation
    public function read($conditions = []) {
        try {
            $sql = "SELECT * FROM " . $this->table_name;

            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $clauses = [];
                foreach ($conditions as $key => $value) {
                    $clauses[] = "$key = :$key";
                }
                $sql .= implode(" AND ", $clauses);
            }

            $stmt = $this->conn->prepare($sql);

            // Bind values dynamically
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Read error: " . $exception->getMessage();
            return [];
        }
    }

    // Update Operation
    public function update($id, $data) {
        try {
            $columns = [];
            foreach ($data as $key => $value) {
                $columns[] = "$key = :$key";
            }
            $sql = "UPDATE " . $this->table_name . " SET " . implode(", ", $columns) . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            // Bind values dynamically
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
            }
            $stmt->bindValue(":id", htmlspecialchars(strip_tags($id)));

            return $stmt->execute();
        } catch(PDOException $exception) {
            echo "Update error: " . $exception->getMessage();
            return false;
        }
    }

    // Delete Operation
    public function delete($conditions = []) {
        try {
            $sql = "DELETE FROM " . $this->table_name;

            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $clauses = [];
                foreach ($conditions as $key => $value) {
                    $clauses[] = "$key = :$key";
                }
                $sql .= implode(" AND ", $clauses);
            }

            $stmt = $this->conn->prepare($sql);

            // Bind values dynamically
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
            }

            return $stmt->execute();
        } catch(PDOException $exception) {
            echo "Delete error: " . $exception->getMessage();
            return false;
        }
    }
}

?>
