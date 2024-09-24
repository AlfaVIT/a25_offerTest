<?php
namespace App;
require_once 'Infrastructure/sdbh.php'; use sdbh\sdbh; 
require_once 'Application/sdbhInterface.php';
require_once 'Application/dbAdapter.php';
use dbAdapter; 


class Tariff
{
    public function getTariff()
    {
        $dbh = new sdbh();
		$db = new dbAdapter($dbh);
        $product_id = isset($_POST['product']) ? $_POST['product'] : 1;

		$tariff = $db->get_tariff($product_id);

		foreach ( $tariff as $min_days => $tariff_price ) {
			if ( $min_days==0 ) {
				$tariff_legend = "Базовый тариф";
			} else {
				$tariff_legend = "При аренде от ".$min_days." дней";
			}
			echo "<p><strong>".$tariff_legend.":</strong> ".$tariff_price."</p>";
		}

    }
}

$tariff_obj = new Tariff();
$tariff_obj->getTariff();