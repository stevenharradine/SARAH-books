<?php
	class TitleManager {
		public function getAllBooks () {
				$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`books`
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			
			return $data;
		}
	}