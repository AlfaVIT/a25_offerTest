<?php
namespace App\Application;
require_once '../Domain/Users/UserEntity.php'; use App\Domain\Users\UserEntity;

require_once '../Infrastructure/sdbh.php'; use sdbh\sdbh; 
require_once 'sdbhInterface.php';
require_once 'dbAdapter.php'; use dbAdapter;

class AdminService {

    /** @var UserEntity */
    public $user;

	public $error = "";
	public $post_data = array();

    public function __construct()
    {
        $this->user = new UserEntity();
    }


	/**
	 * Добавление товара в БД из формы, переданной через $_POST
	 * 
	 * @param array $post_data
	 */
    public function addNewProduct($post_data)
    {
        if (!$this->user->isAdmin) return;

		$this->loadForm($post_data);
		$this->validateFormAddProduct();

		if ( $this->error != "" ) {
			return false;
		}

		$dbh = new sdbh();
		$db = new dbAdapter($dbh);

		$rows = array();
		$rows[0]["NAME"] = $this->post_data['product_name'];
		$rows[0]["PRICE"] = $this->post_data['product_price'];
		$rows[0]["TARIFF"] = $this->prepareSerializedTarifData();

		$db->insert_rows("a25_products", $rows);

		return true;
    }


	/**
	 * Загруза полей формы в объект класса
	 * 
	 * @param array $post_data
	 */
	private function loadForm($post_data) 
	{
		$this->post_data['product_name'] = !empty( $post_data['product_name'] ) ? $post_data['product_name'] : false;
		$this->post_data['product_price'] = !empty( $post_data['product_price'] ) ? (int)$post_data['product_price'] : false;
		$this->post_data['tarif_day'] = !empty( $post_data['tarif-day'] ) ? $post_data['tarif-day'] : false;
		$this->post_data['tarif_price'] = !empty( $post_data['tarif-price'] ) ? $post_data['tarif-price'] : false;
	}

	/**
	 * Валидация полей формы
	 */
	private function validateFormAddProduct() 
	{
		if ( $this->post_data['product_name'] === false ) {
			$this->error .= "Вы забыли передать Название товара! ";
		}
		if ( $this->post_data['product_price'] === false ) {
			$this->error .= "Вы забыли передать Стоимость товара! ";
		}
		if ( $this->post_data['product_price'] < 0 ) {
			$this->error .= "Стоимость товара не может быть отрицательной! ";
		}
	}

	/**
	 * Преобразование массива с Тарифом в сериализованную строку
	 */
	private function prepareSerializedTarifData() {
		$tarif = array();
		// сортируем массив, чтобы дни тарифа шли по возрастанию
		asort($this->post_data['tarif_day']);
		foreach ( $this->post_data['tarif_day'] as $key => $tarif_day ) {
			$tarif_price = !empty( $this->post_data['tarif_price'][$key] ) ? $this->post_data['tarif_price'][$key] : 0;
			$tarif[(int)$tarif_day] = (int)$tarif_price;
		}
		if ( count($tarif) == 1 and $tarif[0] === 0 ) {
			return Null;
		} else {
			return serialize($tarif);
		}
	}

}