<?php

require_once(LIB_PATH_INC . DS . "01_constants.php");

class Pdo_DB {
    private $con;
    private $query_id;

    public function __construct() {
        $this->db_connect();
    }

    /*--------------------------------------------------------------*/
    /* Function for Open database connection
    /*--------------------------------------------------------------*/
    private function db_connect() {
        try {
            $this->con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
	
    /*--------------------------------------------------------------*/
    /* Transaction Handling Methods
    /*--------------------------------------------------------------*/
    public function beginTransaction() {
        $this->con->beginTransaction();
    }

    public function commit() {
        $this->con->commit();
    }

    public function rollBack() {
        $this->con->rollBack();
    }

    /*--------------------------------------------------------------*/
    /* Function for Close database connection
    /*--------------------------------------------------------------*/
    public function db_disconnect() {
        $this->con = null;
    }

    /*--------------------------------------------------------------*/
    /* Function for PDO query
    /*--------------------------------------------------------------*/
	public function query($sql, $params = []) {
		try {
			$stmt = $this->con->prepare($sql);
			foreach ($params as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();
			$this->query_id = $stmt;

			return $stmt; // Return the prepared statement directly for further use
		} catch (PDOException $e) {
			if ($this->con->inTransaction()) {
				$this->rollBack(); // Only roll back if there is an active transaction
			}
			die("Query failed: " . $e->getMessage() . "<br>SQL: " . htmlspecialchars($sql));
		}
	}

    /*--------------------------------------------------------------*/
    /* Function for Fetching results
    /*--------------------------------------------------------------*/
    public function fetch_array($statement) {
        return $statement->fetch(PDO::FETCH_BOTH);
    }

    public function fetch_object($statement) {
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public function fetch_assoc($statement) {
        return $statement->fetch(); // Uses the default fetch mode (FETCH_ASSOC)
    }

    /*--------------------------------------------------------------*/
    /* Function for Counting rows
    /*--------------------------------------------------------------*/
    public function num_rows($statement) {
        return $statement->rowCount();
    }

    /*--------------------------------------------------------------*/
    /* Function for Getting last inserted ID
    /*--------------------------------------------------------------*/
    public function insert_id() {
        return $this->con->lastInsertId();
    }

    /*--------------------------------------------------------------*/
    /* Function for Counting affected rows
    /*--------------------------------------------------------------*/
    public function affected_rows() {
        return $this->query_id ? $this->query_id->rowCount() : 0;
    }

    /*--------------------------------------------------------------*/
    /* Function for Escaping strings (No need to add extra quotes in query)
    /*--------------------------------------------------------------*/
    public function escape($str) {

        return substr($this->con->quote($str), 1, -1); // Remove surrounding quotes added by PDO
    }

    /*--------------------------------------------------------------*/
    /* Function for While loop
    /*--------------------------------------------------------------*/
    public function while_loop($statement) {
        $results = [];
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $result;
        }
        return $results;
    }
}

// Instancia de la clase Pdo_DB
$db = new Pdo_DB();
