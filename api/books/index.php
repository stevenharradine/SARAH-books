<?php

	$relative_base_path = '../../../../';
	require_once '../../../../views/_secureHead.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$BOOK_ID = request_isset ('BOOK_ID');
		$action = request_isset ('action', 'get');

		$author = request_isset ('author');
		$title = request_isset ('title');
		$epub = request_isset ('epub');

		if ($action == 'get') {
			if ($BOOK_ID) {
				$books_data = BooksManager::getBook ($BOOK_ID);
				$books_row = mysql_fetch_array( $books_data );

				$BOOK_ID = $books_row['BOOK_ID'];
				$author = $books_row['author'];
				$title = $books_row['title'];
				$epub = $books_row['epub'];

				$output = <<<EOD
{
	"BOOK_ID":"$BOOK_ID",
	"author":"$author",
	"title":"$title",
	"epub":"$epub"
}
EOD;
			} else {
				$output = '[';
				$books_data = BooksManager::getAllBooks ();
				while (($books_row = mysql_fetch_array( $books_data ) ) != null) {
					$BOOK_ID = $books_row['BOOK_ID'];
					$author = $books_row['author'];
					$title = $books_row['title'];
					$epub = $books_row['epub'];

					$output .= <<<EOD
{
	"BOOK_ID":"$BOOK_ID",
	"author":"$author",
	"title":"$title",
	"epub":"$epub"
},
EOD;
				}
				$output = rtrim ($output, ',');
				$output .= "]";
			}

			echo $output;
		} else if ($action == "post") {
			$status = BooksManager::addBook ($author, $title, $epub);

			if ($status) {
				$BOOK_ID = mysql_insert_id();

				header('Location: /SARAH/apps/books/api/books/?BOOK_ID=' . $BOOK_ID);
			}
		} else if ($action == "delete") {
			$status = BooksManager::deleteBook ($BOOK_ID);

			if ($status) {
				http_response_code(200);
			} else {
				http_response_code(404);
			}
		}
	}