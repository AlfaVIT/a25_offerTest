<?php
namespace App;
require_once 'Infrastructure/sdbh.php'; use sdbh\sdbh; 
require_once 'Application/sdbhInterface.php';
require_once 'Application/dbAdapter.php';
use dbAdapter;

class Calculate
{
    public function calculate1()
    {
        $dbh = new sdbh();
		$db = new dbAdapter($dbh);
        //$days = isset($_POST['days']) ? $_POST['days'] : 0;
        $days_start = !empty($_POST['daysStart']) ? $_POST['daysStart'] : false;
        $days_end = !empty($_POST['daysEnd']) ? $_POST['daysEnd'] : false;
        $product_id = isset($_POST['product']) ? $_POST['product'] : 0;
        $selected_services = isset($_POST['services']) ? $_POST['services'] : [];
        $product = $db->make_query("SELECT * FROM a25_products WHERE ID = $product_id");
        if ($product) {
            $product = $product[0];
            $price = $product['PRICE'];
            $tarif = $product['TARIFF'];
        } else {
            echo "Ошибка, товар не найден!";
            return;
        }

		// рассчитываем срок аренда на основе дат
		if ( $days_start === false and $days_end === false ) {
			$days = 0;
		} elseif ( ( $days_start !== false and $days_end === false ) 
				or ( $days_start === false and $days_end !== false )  ) {
			$days = 1;
		} else {
			$date1 = date_create($days_start);
			$date2 = date_create($days_end);
			$interval = date_diff($date1, $date2);
			$days = $interval->format("%a")+1;
		}

		$tarifs = unserialize($tarif);
        if (is_array($tarifs)) {
            $product_price = $price;
            foreach ($tarifs as $day_count => $tarif_price) {
                if ($days >= $day_count) {
                    $product_price = $tarif_price;
                }
            }
            $total_price = $product_price * $days;
        }else{
            $total_price = $price * $days;
        }

        $services_price = 0;
        foreach ($selected_services as $service) {
            $services_price += (float)$service * $days;
        }

        $total_price += $services_price;

        echo $total_price;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instance = new Calculate();
    $instance->calculate1();
}
