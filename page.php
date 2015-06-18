<?php
	require_once '../../views/_secureHead.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		$page = request_isset ('page');

		include $relative_base_path . 'views/_generic.php';
	}