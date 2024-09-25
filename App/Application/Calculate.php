<?php
require_once '../Infrastructure/sdbh.php'; use sdbh\sdbh; 
require_once 'sdbhInterface.php';
require_once 'dbAdapter.php';

class Calculate
{
	public $days;
	public $current_tarif;
	public $services_price = 0; 

	private $days_start;
	private $days_end;
	private $selected_services;
	private $product;

	public $total_price = 0;

	public function __construct( $postData )
	{
		$dbh = new sdbh();
		$db = new dbAdapter($dbh);
		$this->days_start = !empty($_POST['daysStart']) ? $_POST['daysStart'] : false;
		$this->days_end = !empty($_POST['daysEnd']) ? $_POST['daysEnd'] : false;
		$this->selected_services = isset($_POST['services']) ? $_POST['services'] : [];

		$product_id = isset($_POST['product']) ? $_POST['product'] : 1;
		$this->product = $db->make_query("SELECT * FROM a25_products WHERE ID = $product_id");

	}

	/**
	 * Подсчёт стоимость
	 */
	public function calculate() {
		$this->getDaysFromInterval();
		$this->calculateRent();
		$this->calculateService();
	}


	/**
	 * Получение количества дней между датами
	 * 
	 * @return integer
	 */
	private function getDaysFromInterval()
	{
		if ( $this->days_start === false and $this->days_end === false ) {
			$days = 0;
		} elseif ( ( $this->days_start !== false and $this->days_end === false ) 
				or ( $this->days_start === false and $this->days_end !== false )  ) {
			$days = 1;
		} else {
			$date1 = date_create($this->days_start);
			$date2 = date_create($this->days_end);
			$interval = date_diff($date1, $date2);
			$days = $interval->format("%a")+1;
		}
		$this->days = $days;
	}

	/**
	 * Подсчёт стоимости дополнительных услуг
	 */
	private function calculateService() 
	{
		$services_price = 0;
        foreach ($this->selected_services as $service) {
            $services_price += (float)$service * $this->days;
			$this->services_price += (float)$service;
        }

        $this->total_price += $services_price;
	}

	/**
	 * Подсчёт стоимости аренды
	 */
    public function calculateRent()
    {
		if ($this->product) {
            $product = $this->product[0];
            $price = $product['PRICE'];
            $tarif = $product['TARIFF'];
        } else {
            echo "Ошибка, товар не найден!";
            return;
        }

		$tarifs = unserialize($tarif);
        if (is_array($tarifs)) {
			ksort($tarifs);
            $product_price = $price;
            foreach ($tarifs as $day_count => $tarif_price) {
                if ($this->days >= $day_count) {
                    $product_price = $tarif_price;
                }
            }
            $this->total_price = $product_price * $this->days;
			$this->current_tarif = $product_price;
        }else{
            $this->total_price = $price * $this->days;
			$this->current_tarif = $price;
        }

    }

}
