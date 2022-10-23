<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TopMenu extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	// Метод генерации верхнего меню
	public function generate($item) {
		if ($item === 0) {
			return "<li class = 'active'><a href = '" . base_url() . "' class = 'top-link'>Результаты</a></li>" . 
				   "<li><a href = '" . base_url() . "index.php/main/available' class = 'top-link'>Доступные тесты</a></li>";
		} else if ($item == 1) {
			return "<li><a href = '" . base_url() . "' class = 'top-link'>Результаты</a></li>" .
				   "<li class = 'active'><a href = '" . base_url() . "index.php/main/available' class = 'top-link'>Доступные тесты</a></li>";
		} else {
			return "<li><a href = '" . base_url() . "' class = 'top-link'>Результаты</a></li>" .
				   "<li><a href = '" . base_url() . "index.php/main/available' class = 'top-link'>Доступные тесты</a></li>";
		}
	}

	// Метод генерации верхнего меню для АДМИНА
	public function generate_admin($isAdmin, $item) {
		if (!$isAdmin) {
			if ($item === 0) {
				return "<li class = 'active'><a href = '" . base_url() . "index.php/admin' class = 'top-link'>Результаты</a></li>" . 
					   "<li><a href = '" . base_url() . "index.php/admin/add_test' class = 'top-link'>Добавить тест</a></li>" . 
					   "<li><a href = '" . base_url() . "index.php/admin/edit_test' class = 'top-link'>Редактирование тестов</a></li>";
			} else if ($item == 1) {
				return "<li><a href = '" . base_url() . "index.php/admin' class = 'top-link'>Результаты</a></li>" .
					   "<li class = 'active'><a href = '" . base_url() . "index.php/admin/add_test' class = 'top-link'>Добавить тест</a></li>" .
					   "<li><a href = '" . base_url() . "index.php/admin/edit_test' class = 'top-link'>Редактирование тестов</a></li>";
			} else if ($item == 2) {
				return "<li><a href = '" . base_url() . "index.php/admin' class = 'top-link'>Результаты</a></li>" .
					   "<li><a href = '" . base_url() . "index.php/admin/add_test' class = 'top-link'>Добавить тест</a></li>" .
					   "<li class = 'active'><a href = '" . base_url() . "index.php/admin/edit_test' class = 'top-link'>Редактирование тестов</a></li>";
			} else {
				return "<li><a href = '" . base_url() . "index.php/admin' class = 'top-link'>Результаты</a></li>" .
					   "<li><a href = '" . base_url() . "index.php/admin/add_test' class = 'top-link'>Добавить тест</a></li>" .
					   "<li><a href = '" . base_url() . "index.php/admin/edit_test' class = 'top-link'>Редактирование тестов</a></li>";
			}
		} else {
			if ($item == 0) {
				return "<li class = 'active'><a href = '" . base_url() . "index.php/admin/students' class = 'top-link'>Ученики</a></li>" . "<li><a href = '" . base_url() . "index.php/admin/teachers' class = 'top-link'>Учителя</a></li>";
			} else {
				return "<li><a href = '" . base_url() . "index.php/admin/students' class = 'top-link'>Ученики</a></li>" . "<li class = 'active'><a href = '" . base_url() . "index.php/admin/teachers' class = 'top-link'>Учителя</a></li>";
			}
		}
	}

}

?>