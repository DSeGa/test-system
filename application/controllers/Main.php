<?php

	defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function _remap($method , $params = array()) {
		if ($method == 'index') {
			$this->$method();
		} else if ($method == 'do_login') {
			$this->$method();
		} else if ($method == 'do_logout') {
			$this->$method();
		} else if ($method == 'available') {
			$this->$method();
		} else if ($method == 'do_test') {
			$this->$method($params[0]);
		} else if ($method == 'check_test') {
			$this->$method();
		} else if ($method == 'view_result') {
			$this->$method($params[0]);
		} else {
			$this->wrong_params();
		}
	}


	// главная форма, история
	public function index() {
		if ( !$this->session_started() ) {
			$this->load->view('login_view');
		} else {
			$this->load->model('Student');
			$data_header['name'] = $this->Student->get_name();
			$this->load->model('TopMenu');
			$data_header['top_menu'] = $this->TopMenu->generate(0);
			$this->load->view('header_view' , $data_header);
			
			// Сначала нужно узнать историю...
			$this->load->model('Test');
			$data_history['history'] = $this->Test->load_history();
			$this->load->view('history_view' , $data_history);

			$this->load->view('footer_view');
		}
	}


	// Список доступных тестов
	public function available() {
		if ( !$this->session_started() ) {
			redirect('' , 'refresh');
		} else {
			$this->load->model('Student');						
			if ($this->Student->has_exam()) {
				$test_id = $this->Student->get_current_test_id();
				redirect(base_url() . "index.php/main/do_test/" . $test_id , "refresh");
			} else {				
				$data_header['name'] = $this->Student->get_name();
				$this->load->model('TopMenu');
				$data_header['top_menu'] = $this->TopMenu->generate(1);
				$this->load->view('header_view' , $data_header);
				$this->load->model('Subject');
				$data_available['subjects'] = $this->Subject->get_subjects();
				$data_available['tests'] = $this->Subject->get_tests();
				$this->load->view('available_view' , $data_available);
				$this->load->view('footer_view');
			}			
		}
	}


	// Начать выполнение теста
	public function do_test($test_id) {
		if ( !$this->session_started() ) redirect('' , 'refresh');

		$this->load->model('Student');
		$data_header['name'] = $this->Student->get_name();
		$this->load->model('TopMenu');
		$data_header['top_menu'] = $this->TopMenu->generate(1);
		$this->load->view('header_view' , $data_header);
		
		$this->load->model('Test');
		$data_test['test'] = $this->Test->get_questions($test_id);
		$data_test['test_id'] = $test_id;
		$this->load->view('do_test_view' , $data_test);
	}

	// Проверяем тест, переносим его в раздел истории. После проверки сразу на просмотр результата для этого теста!
	public function check_test() {
		if ( !$this->session_started() ) redirect('' , 'refresh');
		
		$this->load->model('Test');
		$result_id = $this->Test->check_test($_POST['test_id']);

		redirect(base_url() . "index.php/main/view_result/" . $result_id , 'refresh');		

	}


	public function view_result($result_id) {
		if ( !$this->session_started() ) redirect('' , 'refresh');
		
		$this->load->model('Student');
		$data_header['name'] = $this->Student->get_name();
		$this->load->model('TopMenu');
		$data_header['top_menu'] = $this->TopMenu->generate(0);
		$this->load->view('header_view' , $data_header);

		$this->load->model('Test');
		$data['history'] = $this->Test->get_result($result_id);
		$data['back'] = base_url();
		$data['lastname'] = "";
		$this->load->view('result_view' , $data);
		
	}

	// Попытка войти в систему
	public function do_login() {
		$this->load->model('Student');
		$ret = $this->Student->do_login();
		if ($ret == 0) {
			redirect(base_url() , 'refresh');
		} else {
			$this->load->model('User');
			$ret = $this->User->do_login();
			if ($ret == 0) {
				$isAdmin = $this->is_user_admin();
				if ($isAdmin) {
					redirect(base_url() . "index.php/admin/students" , 'refresh');
				} else {
					redirect(base_url() . "index.php/admin" , 'refresh');
				}
			} else {
				redirect(base_url() , 'refresh');
			}			
		}		
	}

	// Выход из системы
	public function do_logout() {
		$this->load->model('Student');
		$this->Student->do_logout();
		redirect('' , 'refresh');
	}


	public function wrong_params() {
		$this->load->view('error_view');
	}


	// Проверка, был ли произведен вход в систему
	private function session_started() {
		return (isset($_SESSION['student_id']));
	}

		// Получить тип пользователя
	private function is_user_admin() {
		$subject_id = $_SESSION['subject_id'];
		return ($subject_id == 0);
	}

}
