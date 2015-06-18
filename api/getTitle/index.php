<?php

	$relative_base_path = '../../../../';
	require_once '../../../../views/_secureHead.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$BOOK_ID = request_isset ('BOOK_ID');
		$action = request_isset ('action', 'get');

		$book_url = request_isset ('book_url');

		if ($action == 'get') {
//			echo "Title";
			$cmd = "cd /tmp/ && wget '$book_url'";
//			shell_exec($cmd);

			$book_url_split = explode ( '/' , $book_url );

			$zip = new ZipArchive;
			$zip_path = '/tmp/' . $book_url_split[count($book_url_split) - 1];
			$extractTo_path = '/tmp/extracted/' . $book_url_split[count($book_url_split) - 1] . '/';

			$res = $zip->open($zip_path);
			if ($res === TRUE) {
			    echo 'ok';
			    $zip->extractTo($extractTo_path);
			    $zip->close();
			} else {
			    echo 'failed, code:' . $res;
			}
		}
	}