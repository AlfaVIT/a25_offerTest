<?php
require_once 'App/Domain/Users/UserEntity.php'; use App\Domain\Users\UserEntity;

$user = new UserEntity();
if (!$user->isAdmin) die('Доступ закрыт');
?>
<html>
<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link href="assets/css/style.css" rel="stylesheet"/>
</head>
<body>

<div class="container">

	<div class="row row-header">
		<div class="col-12" id="count">
			<img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
			<h1 class="text-primary">Админка</h1>
		</div>
	</div>

	<div class="row row-form">
		<div class="col-12">
			<div id="alert-block">
			</div>
			<h4>Добавление товара</h4>
			<form action="App/Presentation/adminAddProductPresentation.php" method="POST" id="form-addProduct">
				<div class="input-group mb-3">
					<span></span>
					<span class="input-group-text" id="product_name">Название</span>
					<input type="text" class="form-control" placeholder="NAME" name="product_name" required>
				</div>
				<div class="input-group mb-3">
					<span class="input-group-text" id="product_price">Стоимость</span>
					<input type="number" class="form-control" placeholder="PRICE" name="product_price" min="0" required>
				</div>
				<div class="tarif-block">
					<h6>Тариф</h6>
					<div class="input-group basestring mb-3">
						<input type="number" class="form-control" min="0" placeholder="от N дней" aria-label="от N дней" name="tarif-day[]">
						<input type="number" class="form-control" min="0" placeholder="стоимость" aria-label="стоимость" name="tarif-price[]">
						<button class="btn btn-success" type="button" onclick="tarif_dublicate($(this))">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
							</svg>
						</button>
					</div>
				</div>

				<button type="submit" class="btn btn-lg btn-success">Добавить товар</button>

			</form>
		</div>
	</div>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
	function tarif_dublicate(target) {
		var svg_del = "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'><path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5'/></svg>";

		$tarif_string = $(target.parent(".input-group")).clone();
		$tarif_string.removeClass("basestring");
		$tarif_string.children("input[type=number]").val("");
		$tarif_string.children("button").removeClass("btn-success").addClass("btn-warning").attr("onclick", "tarif_removeString($(this))").html(svg_del);
		$(".tarif-block").append($tarif_string);
	}

	function tarif_removeString(target) {
		target.parent(".input-group").slideUp(200, function(event){
			$(this).remove();
		});
	}


    $(document).ready(function() {

		$("#form-addProduct").submit(function(event) {
            event.preventDefault();

			$.ajax({
                url: 'App/Presentation/adminAddProductPresentation.php',
                type: 'POST',
                data: $(this).serialize(),
				dataType: 'JSON',
                success: function(data) {

					if ( data['error'] != "" ) {
						$("#alert-block").append(data['error']);
					} else if ( data['result'] != "" ) {
						$("#alert-block").append(data['result']);
						$(".tarif-block .input-group:not(.basestring)").slideUp(200, function() {
							$(this).remove();
						});
						$("#form-addProduct input").val("");
					}

				},
                error: function() {
					alert("Ошибка соединения!");
                }
            });
		})

	})

</script>
</body>
</html>