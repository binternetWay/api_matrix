<?php

namespace App;

class Connection {
	public static function getDb() {
		try {

			$conn = new \PDO(
				"pgsql:host=191.242.48.3;port=5432;dbname=dbemp00372",
				"cliente_s",
				"8hnHjcBu2e5TkWGx" 
			);

			return $conn;

		} catch (\PDOException $e) {
			echo 'erro ao conectar com o banco';
			echo $e;
		}
	}
}

abstract class Model {

	protected $db;

	public function __construct(\PDO $db) {//Executa a conexão com o banco 
		$this->db = $db;
	}
}

?>