
<?php

	interface sdbhInterface 
	{
		public function make_query($query);
		public function insert_rows($tbl_name, $rows);
		public function mselect_rows(
			$tbl_name,
			$select_array,
			$from, $amount,
			$order_by,
			$order = Null,
			$deadlock_up = False,
			$lock_mode = Null
		);

		// добавляем в интерфейс новый метод, чтобы не забыть его реализовать в будущем
		public function get_tariff($product_id);

	}

?>