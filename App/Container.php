<?php

namespace App;

use App\Connection;

class Container {
	
  //Modelo a ser instânciado
	public static function getModel($model) {
		$class = "\\App\\Models\\".ucfirst($model);
		$conn = Connection::getDb();

		return new $class($conn);
	}
}
?>