<?php
require_once 'App/Infrastructure/sdbh.php'; use sdbh\sdbh;
$dbh = new sdbh();
require_once 'App/Application/sdbhInterface.php';
require_once 'App/Application/dbAdapter.php';
$db = new dbAdapter($dbh);
?>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet"/>
    <link href="assets/css/air-datepicker.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row row-header">
        <div class="col-12" id="count">
            <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
            <h1>Прокат Y</h1>
        </div>
    </div>

    <div class="row row-form">
        <div class="col-12">
            <form action="App/calculate.php" method="POST" id="form">

                <?php $products = $db->make_query('SELECT * FROM a25_products');
                if (is_array($products)) { ?>
                    <label class="form-label" for="product">Выберите продукт:</label>
                    <select class="form-select" name="product" id="product">
                        <?php foreach ($products as $product) {
                            $name = $product['NAME'];
                            $price = $product['PRICE'];
                            $tarif = $product['TARIFF'];
                            ?>
                            <option value="<?= $product['ID']; ?>"><?= $name; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>

                <label for="customRangeStart" class="form-label" id="countStart">Начало аренды:</label>
                <input type="text" name="daysStart" class="form-control" id="customRangeStart">

                <label for="customRangeEnd" class="form-label" id="countEnd">Окончание аренды:</label>
                <input type="text" name="daysEnd" class="form-control" id="customRangeEnd">

                <?php $services = unserialize($db->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id')[0]['set_value']);
                if (is_array($services)) {
                    ?>
                    <label for="customRange1" class="form-label">Дополнительно:</label>
                    <?php
                    $index = 0;
                    foreach ($services as $k => $s) {
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="<?= $s; ?>" id="flexCheck<?= $index; ?>">
                            <label class="form-check-label" for="flexCheck<?= $index; ?>">
                                <?= $k ?>: <?= $s ?>
                            </label>
                        </div>
                    <?php $index++; } ?>
                <?php } ?>

                <button type="submit" class="btn btn-primary">Рассчитать</button>
            </form>

            <h5>Итоговая стоимость: <span id="total-price"></span></h5>

            <h5>Тариф:</h5>
            <span id="tariff">
            <?php include("App/tariff.php"); ?>
            </span>

		</div>
    </div>
</div>

<script src="assets/js/air-datepicker.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#form").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: 'App/Presentation/calculatePresentation.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
					$(".tooltip").hide();
                    $("#total-price").html(response);
					$('[data-toggle="tooltip"]').tooltip();
                },
                error: function() {
                    $("#total-price").text('Ошибка при расчете');
                }
            });
        });


		$("#product, #form input[type=checkbox]").on("change", function(event) {
			$("#form").submit();
		})
		$("#product").on("change", function(event) {
            $.ajax({
                url: 'App/tariff.php',
                type: 'POST',
                data: {'product':$(this).val()},
                success: function(response) {
                    $("#tariff").html(response);
                },
                error: function() {
                    $("#tariff").html('Ошибка передачи данных');
                }
            });
		})

	});


	// air-datepicker init
	var AirDatepicker_settings = {
		isMobile: true,
		autoClose: true,
		locale: {
			dateFormat: 'yyyy-MM-dd'
		},
		onHide(isFinished) {
			if (isFinished) {
				$("#form").submit();
			}
		}
	};
	new AirDatepicker("#customRangeStart", AirDatepicker_settings);
	new AirDatepicker("#customRangeEnd", AirDatepicker_settings);
	// air-datepicker init end
</script>
</body>
</html>