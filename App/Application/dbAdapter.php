<?php

	class dbAdapter implements sdbhInterface
	{

		private $sdbh;

		public function __construct($sdbh)
		{
			$this->sdbh = $sdbh;
		}

		/**
		 * Простой запрос к БД
		 * 
		 * @param string query - строка запроса
		 * @return array|string
		 */
		public function make_query($query)
		{
			return $this->sdbh->make_query($query);
		}

		/**
		 * Функция производит SELECT запрос на основе входных параметров
		 * 
		 * @param string $tbl_name - Имя таблицы
		 * @param array $select_array - Массив запрашиваемых полей
		 * @param integer $from , $amount - Лимиты
		 * @param string $order_by - Поле для сортировки
		 * @param string $order - Порядок сортировки ("DESC" or Null)
		 * @param boolean $deadlock_up - флаг, определяющий выбрасывать ли исключение
		 * @param string $lock_mode - Режим блокировки (LISH - LOCK IN SHARE MODE, FU - FOR UPDATE or Null - не блокировать)
		 * @return array Ассоциативный массив
		 */
		public function mselect_rows(
			$tbl_name,
			$select_array,
			$from, $amount,
			$order_by,
			$order = Null,
			$deadlock_up = False,
			$lock_mode = Null
		)
		{
			$tbl_name = $this->sdbh->escape_string($tbl_name);
			$order_by = $this->sdbh->escape_string($order_by);
			return $this->sdbh->mselect_rows($tbl_name, $select_array, $from, $amount, $order_by, $order, $deadlock_up, $lock_mode);
		}


		/**
		 * Добавление записи в таблицу
		 * @param string $tbl_name - Имя таблицы
		 * @param array $rows - ассоциативный массив {"поле" => "значение"}
		 * @return integer число добавленных записей
		 */
		public function insert_rows($tbl_name, $rows) 
		{
			return $this->sdbh->insert_rows($tbl_name, $rows);
		}


		/**
		 * Специальный метод получения тарифной сетки
		 * 
		 * @param string $product_id - ID продукта
		 * @return array Ассоциативный массив
		 */

		public function get_tariff($product_id) {

			$result = $this->make_query("SELECT `PRICE`, `TARIFF` FROM a25_products WHERE ID = '". $product_id ."'");
			$tariff = !empty($result[0]["TARIFF"]) ? unserialize($result[0]["TARIFF"]) : array();
			// базовый тариф должен быть не дешевле прайса
			if ( empty($tariff[0]) ) {
				$tariff[0] = $result[0]["PRICE"];
			}
			
			return $tariff;
		}

	}

?>