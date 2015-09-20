<?php
	class BooksManager {
		public function addBookmark ($book, $bookmark) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$currentBookMarkIndex = BooksManager::getBookmarkId ($book);

			if ($currentBookMarkIndex < 0) {	// no bookmark exists for this book
												// insert new bookmark
				$sql = <<<EOD
	INSERT INTO `sarah`.`books_bookmarks` (
		`USER_ID`,
		`book`,
		`xpath`
	) VALUES (
		'$USER_ID',
		'$book',
		'$bookmark'
	);
EOD;
			} else {							// a bookmark exists
												// update the existing bookmark
				$sql = <<<EOD
	UPDATE `sarah`.`books_bookmarks`
	SET
		`xpath` = '$bookmark'
	WHERE
		`book` = '$book'
			AND
		`USER_ID` = $USER_ID;
EOD;
			}

			return mysql_query($sql) or die(mysql_error());
		}

		public function getBookmarkId ($book) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT `BOOKS_BOOKMARK_ID`
	FROM `sarah`.`books_bookmarks`
	WHERE
		`book` = "$book"
			AND
		`USER_ID` = $USER_ID;
EOD;

			$data = mysql_query($sql) or die(mysql_error());

			$row = mysql_fetch_array ($data);

			return is_null($row['BOOKS_BOOKMARK_ID']) ? -1 : $row['BOOKS_BOOKMARK_ID'];
		}

		public function getBookmarkPath ($book) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT `xpath`
	FROM `sarah`.`books_bookmarks`
	WHERE
		`book` = "$book"
			AND
		`USER_ID` = $USER_ID;
EOD;

			$data = mysql_query($sql) or die(mysql_error());

			$row = mysql_fetch_array ($data);

			return $row['xpath'];
		}

		public function getPageSettings () {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT `line-height`, `font-size`
	FROM `sarah`.`books_pagesettings`
	WHERE
		`USER_ID` = $USER_ID;
EOD;

			$data = mysql_query($sql) or die(mysql_error());
			$row = mysql_fetch_array ($data);

			return array (
				'line-height'	=> $row['line-height'],
				'font-size'		=> $row['font-size']
			);
		}
		public function saveFontSize ($fontSize) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

				$sql = <<<EOD
	UPDATE `sarah`.`books_pagesettings`
	SET
		`font-size` = '$fontSize'
	WHERE
		`USER_ID` = $USER_ID;
EOD;

			return mysql_query($sql) or die(mysql_error());
		}
		public function saveLineHeight ($lineHeight) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

				$sql = <<<EOD
	UPDATE `sarah`.`books_pagesettings`
	SET
		`line-height` = '$lineHeight'
	WHERE
		`USER_ID` = $USER_ID;
EOD;

			return mysql_query($sql) or die(mysql_error());
		}
		public function getAllBooks () {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

				$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`books`
EOD;

			$data = mysql_query($sql) or die(mysql_error());
			$books = Array ();

			while (($row = mysql_fetch_array( $data )) != null) {
				$books[] = Array (
	        'id' => $row['BOOK_ID'],
	        'author' => $row['author'],
	        'title' => $row['title'],
	        'epub' => $row['path']
				);
			}
			
			return $books;
		}
	}