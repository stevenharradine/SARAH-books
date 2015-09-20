<?php
  require_once '../../views/_secureHead.php';

  function restServiceCall ($url, $data) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    // just accept the sites cert (still encrypts connection, just does not validate the cert)
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // dont output on curl (let me decide when / if to show that raw)
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

    // pass post variable (in this case authentication)
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($curl);
    curl_close($curl);

    return json_decode($result, TRUE);
  }

  if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
    $id = request_isset ('id');
    $book = request_isset ('book');
    $bookmark = request_isset ('bookmark');
    $getBookmark = request_isset ('getbookmark');
    $getPageSettings = request_isset ('getpagesettings');
    $saveFontSize = request_isset ('saveFontSize');
    $saveLineHeight = request_isset ('saveLineHeight');

    $search_target = 'books';
    $searchModel = new SearchModel( $search_target );
    $booksModel = new TableModel ( '', $search_target);

    $alt_menu = ButtonView::render (new ButtonModel(IconView::render( new IconModel ('gear', 'configure')), '#', 'page-settings'));

    $views_to_load = array();

    if (is_null ($book) && is_null($getBookmark) && is_null($getPageSettings)) {
/*      $addBooks_data = array (
        "myusername" => $username,
        "mypassword" => $password,
        "action" => "post",
        "title" => "1984",
        "author" => "George Orwell",
        "epub" => "https://localhost/media/Music/Library/Books/george%20orwell/George%20Orwell/1984%20(645)/1984%20-%20George%20Orwell.epub"
      );
      restServiceCall ($endpoint, $addBooks_data);
*/
      $books = BooksManager::getAllBooks ();

      for ($i = 0; $i < count ($books); $i++) {
        echo $books[$i];
        $id = $books[$i]['BOOK_ID'];
        $author = $books[$i]['author'];
        $title = $books[$i]['title'];
        $epub = $books[$i]['epub'];

        $booksModel->addRow ( array (
          TableView2::createCell ('books', "<a href=\"?book=$epub\">$author - $title</a>" )
        ));
      }
    }

    $views_to_load[] = ' ' . TableView2::render($booksModel);

    if (!is_null ($book) && !is_null ($bookmark)) {
      BooksManager::addBookmark ($book, $bookmark);
    }

    if (!is_null($getBookmark)) {
      echo BooksManager::getBookmarkPath ($getBookmark);  
    }

    if (!is_null($getPageSettings)) {
      $pageSettings = BooksManager::getPageSettings ();
      
      echo $pageSettings[$getPageSettings];
    }

    if (!is_null($saveFontSize)) {
      BooksManager::saveFontSize ($saveFontSize);
    }

    if (!is_null($saveLineHeight)) {
      BooksManager::saveLineHeight ($saveLineHeight);
    }

    include $relative_base_path . 'views/_generic.php';
  }