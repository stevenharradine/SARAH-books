<?php
	class BooksManager {
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
		public function getBook ($BOOK_ID) {
				$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`books`
	WHERE
		`BOOK_ID`='$BOOK_ID'
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			
			return $data;
		}

		public function addBook ($author, $title, $epub) {
				$sql = <<<EOD
	INSERT INTO
		`sarah`.`books`
	(`author`, `title`, `epub`)
		VALUES
	('$author', '$title', '$epub');
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			
			return $data;
		}

		public function deleteBook ($BOOK_ID) {
				$sql = <<<EOD
	DELETE FROM
		`sarah`.`books`
	WHERE
		`books`.`BOOK_ID` = '$BOOK_ID'
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			
			return $data;
		}
	}