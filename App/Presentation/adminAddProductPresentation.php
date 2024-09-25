<?php
	
	require_once "../Application/AdminService.php"; use App\Application\AdminService;

	function alertBuilder($msg_text, $alert_type="danger") {
		$alert = "<div class='alert alert-". $alert_type ." alert-dismissible fade show' role='alert'>". $msg_text ." <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Закрыть'></button></div>";
		return $alert;
	}

	$data['error'] = "";
	$data['result'] = "";

	if ( !empty( $_POST ) ) {
		$adminService = new AdminService();

		if ( $adminService->addNewProduct($_POST) === false ) {
			$data['error'] = alertBuilder("ВНИМАИНЕ! ".$adminService->error);
		} else {
			$data['result'] = alertBuilder("ОТЛИЧНО! Товар добавлен в БД!", "success");
		}
	} else {
		$data['error'] = alertBuilder("<strong>ВНИМАНИЕ!</strong> Ошибка передачи данных!");
	}



	// ответ выдаём массивом JSON
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data);
