<?php

	defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function _remap($method , $params = array()) {
		if ($method == 'index') {
			$this->$method();
		} else if ($method == 'do_logout') {
			$this->$method();
		} else if ($method == 'view_result') {
			$this->$method($params[0]);
		} else if ($method == 'add_test') {
			$this->$method();
		} else if ($method == 'add_new_test') {
			$this->$method();
		} else if ($method == 'add_new_question') {
			$this->$method();
		} else if ($method == 'save_new_question') {
			$this->$method();
		} else if ($method == 'do_report') {
			$this->$method($params[0]);
		} else if ($method == 'edit_test') {
			$this->$method();
		} else if ($method == 'edit_question') {
			$this->$method();
		} else if ($method == 'students') {
			$this->$method();
		} else if ($method == 'add_new_student') {
			$this->$method();
		} else if ($method == 'remove_student') {
			$this->$method($params[0]);
		} else if ($method == 'teachers') {
			$this->$method();
		} else if ($method == 'add_new_user') {
			$this->$method();
		} else if ($method == 'remove_user') {
			$this->$method($params[0]);
		} else if ($method == 'export') {
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
			$isAdmin = $this->is_user_admin();
			if (!$isAdmin) {
				$this->load->model('User');
				$data_header['name'] = $this->User->get_subject();
				$this->load->model('TopMenu');
				$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin, 0);
				$this->load->view('admin_header_view' , $data_header);

				$data_results['results'] = $this->User->get_results();
				$this->load->view('admin_result_view' , $data_results);
				
				$this->load->view('footer_view');
			} else {
				redirect(base_url() . "/index.php/admin/students");
			}
		}
	}

	public function students() {
		if ( !$this->session_started() ) {
			$this->load->view('login_view');
		} else {
			$isAdmin = $this->is_user_admin();
			if (!$isAdmin) {
				redirect(base_url() . "/index.php/admin");
			} else {
				$this->load->model('TopMenu');
				$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin, 0);
				$data_header['name'] = "Администратор";
				$this->load->view('admin_header_view' , $data_header);

				$this->load->model('Student');
				$data_students['students'] = $this->Student->get_all_students();
				$this->load->view('admin_students_view' , $data_students);
			}
		}		
	}

	public function teachers() {
		if ( !$this->session_started() ) {
			$this->load->view('login_view');
		} else {
			$isAdmin = $this->is_user_admin();
			if (!$isAdmin) {
				redirect(base_url() . "/index.php/admin");
			} else {
				$this->load->model('TopMenu');
				$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin, 1);
				$data_header['name'] = "Администратор";
				$this->load->view('admin_header_view' , $data_header);

				$this->load->model('User');
				$data_users['users'] = $this->User->get_all_users();
				$this->load->view('admin_users_view' , $data_users);
			}
		}		
	}


	// Показать результат студента
	public function view_result($result_id) {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			$this->load->model('User');
			$data_header['name'] = $this->User->get_subject();
			$this->load->model('TopMenu');
			$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin , 0);
			$this->load->view('admin_header_view' , $data_header);

			$this->load->model('Test');
			$data['history'] = $this->Test->get_result_admin($result_id);
			$data['back'] = base_url() . "index.php/admin";
			$data['lastname'] = $this->User->get_lastname_by_result_id($result_id);
			$this->load->view('result_view' , $data);		
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	public function add_test() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			$this->load->model('User');
			$data_header['name'] = $this->User->get_subject();
			$this->load->model('TopMenu');
			$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin , 1);
			$this->load->view('admin_header_view' , $data_header);		
			$this->load->view('add_test_subject');
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	// Здесь добавляется тема
	public function add_new_test() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			$this->load->model('Test');
			$this->Test->add_new_test();
			redirect(base_url() . "index.php/admin/add_new_question");
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
		
	}

	// Здесь добавляется вопрос
	public function add_new_question() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			$this->load->model('User');
			$data_header['name'] = $this->User->get_subject();
			$this->load->model('TopMenu');
			$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin , 1);
			$this->load->view('admin_header_view' , $data_header);			
			$data['test_id'] = $this->session->test_id;
			$this->load->model('Test');
			$data['section'] = $this->Test->load_section($data['test_id']);
			$data['total_questions'] = $this->Test->load_question_amount($data['test_id']);
			$this->load->view('add_question_view' , $data);
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	public function save_new_question() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			$this->load->model('Test');
			$this->Test->save_question();
			if (isset($_POST['more'])) {
				redirect(base_url() . "index.php/admin/add_new_question");
			} else if (isset($_POST['finish'])) {
				// Нужно сделать тест доступным
				$this->Test->save_test();
				redirect(base_url() . "index.php/admin");
			}
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	// Метод для редактирования тестов
	public function edit_test() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {

			$this->load->model('User');
			$data_header['name'] = $this->User->get_subject();
			$this->load->model('TopMenu');
			$data_header['top_menu'] = $this->TopMenu->generate_admin($isAdmin , 2);
			$this->load->view('admin_header_view' , $data_header);

			$this->load->model('Test');
			$data['tests'] = $this->Test->get_tests_for_editing();
			$this->load->view('admin_edit_view' , $data);

			$this->load->view('footer_view');
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}

	// Метод для редактирования вопроса
	public function edit_question() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {
			if (isset($_POST['add'])) {
				$this->session->set_userdata('test_id' , $_POST['select_test']);
				redirect(base_url() . "index.php/admin/add_new_question" , "refresh");
			}
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	private function update_border($page , $row , $col , $common_border) {
		$page->getStyleByColumnAndRow($col , $row)->getBorders()->getLeft()->applyFromArray($common_border);
		$page->getStyleByColumnAndRow($col , $row)->getBorders()->getTop()->applyFromArray($common_border);
		$page->getStyleByColumnAndRow($col , $row)->getBorders()->getBottom()->applyFromArray($common_border);
		$page->getStyleByColumnAndRow($col , $row)->getBorders()->getRight()->applyFromArray($common_border);
	}

	public function do_report($test_id) {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');
		$isAdmin = $this->is_user_admin();
		if (!$isAdmin) {

			$this->load->model('Test');
			$section = $this->Test->get_section_by_id($test_id);
			$total_questions = $this->Test->get_total_questions($test_id);
			$results = $this->Test->get_results($test_id);

			require_once(APPPATH . 'libraries\PHPExcel\Classes\PHPExcel.php');
			require_once(APPPATH . 'libraries\PHPExcel\Classes\PHPExcel\Writer\Excel5.php');
			
			$xls = new PHPExcel();
			
			$xls->getProperties()->setTitle("Отчет")
								 ->setSubject($section);
			

			$page = $xls->setActiveSheetIndex(0);

			$common_border = array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array(
							'rgb' => '000000'
						)				
				);


			$page->setCellValue('A1' , '# п/п')
				->setCellValue('B1' , 'ФИО')
				->setCellValue('C1' , 'Кол-во правильных')
				->setCellValue('D1' , 'Оценка');

			for ($column = 0 ; $column < 4 ; $column++) {
				$page->getStyleByColumnAndRow($column , 1)->getFont()->setBold(true);
				$page->getStyleByColumnAndRow($column , 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
				$this->update_border($page , 1 , $column , $common_border);			
			}
			$page->getColumnDimension('A')->setWidth(7);
			$page->getColumnDimension('B')->setWidth(40);
			$page->getColumnDimension('C')->setWidth(30);
			$page->getColumnDimension('D')->setWidth(10);

			$page->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$page->getStyle('A1')->getFill()->getStartColor()->setRGB('EEEEEE');
			$page->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$page->getStyle('B1')->getFill()->getStartColor()->setRGB('EEEEEE');
			$page->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$page->getStyle('C1')->getFill()->getStartColor()->setRGB('EEEEEE');
			$page->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$page->getStyle('D1')->getFill()->getStartColor()->setRGB('EEEEEE');		

			$line = 1;
			foreach ($results->result() as $row) {
				$line++;
				$page->setCellValue('A' . $line , $line - 1);
				$page->setCellValue('B' . $line , $row->NAME);
				$page->setCellValue('C' . $line , $row->POINTS);			
				$percent = ($row->POINTS / $total_questions) * 100; 
				if ($percent < 50) $mark = 2; else if ($percent < 75) $mark = 3; else if ($percent < 90) $mark = 4; else $mark = 5;
				$page->setCellValue('D' . $line , $mark);

				$page->getStyleByColumnAndRow(0 , $line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				$page->getStyleByColumnAndRow(2 , $line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				$page->getStyleByColumnAndRow(3 , $line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				$this->update_border($page , $line , 0 , $common_border);
				$this->update_border($page , $line , 1 , $common_border);
				$this->update_border($page , $line , 2 , $common_border);
				$this->update_border($page , $line , 3 , $common_border);
			}

			$line += 2;		
			$page->setCellValue('B' . $line , 'Всего вопросов');
			$page->getStyleByColumnAndRow(1 , $line)->getFont()->setBold(true);
			$page->setCellValue('C' . $line , $total_questions);			
			$page->getStyleByColumnAndRow(2 , $line)->getFont()->setBold(true);
			
			$page->getStyleByColumnAndRow(1 , $line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);				
			$page->getStyleByColumnAndRow(2 , $line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
			
			$xls->getActiveSheet()->setTitle($section);
			
			$filename = "Отчет_" . $section . ".xlsx";
			$my_header = "Content-Disposition: attachment;filename= " . $filename;

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header($my_header);
			header('Cache-Control: max-age=0');		
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
			header ('Cache-Control: cache, must-revalidate'); 
			header ('Pragma: public'); 

			$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$objWriter->save('php://output');
		} else {
			redirect(base_url() . "/index.php/admin/students");
		}
	}


	// Выход из системы
	public function do_logout() {
		$this->load->model('User');
		$this->User->do_logout();
		redirect('' , 'refresh');
	}


	public function wrong_params() {
		$this->load->view('error_view');
	}


	// Проверка, был ли произведен вход в систему
	private function session_started() {
		return (isset($_SESSION['user_id']));
	}

	// Получить тип пользователя
	private function is_user_admin() {
		$subject_id = $_SESSION['subject_id'];
		return ($subject_id == 0);
	}

	public function add_new_student() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		if ( !$this->is_user_admin() ) redirect(base_url() , 'refresh');
		$fio = $_POST['fio'];
		$grade = $_POST['grade'];
		$this->load->model('Student');
		$this->Student->add_new_student($fio , $grade);
		redirect(base_url() . "/index.php/admin/students" , "refresh");
	}

	public function add_new_user() {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		if ( !$this->is_user_admin() ) redirect(base_url() , 'refresh');
		$subject_name = $_POST['subject_name'];
		$login = $_POST['login'];		
		$this->load->model('User');
		$this->User->add_new_user($subject_name, $login);
		redirect(base_url() . "/index.php/admin/teachers" , "refresh");
	}

	public function remove_student($student_id) {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		if ( !$this->is_user_admin() ) redirect(base_url() , 'refresh');
		$this->load->model('Student');
		$this->Student->remove_student($student_id);
		redirect(base_url() . "/index.php/admin/students" , "refresh");
	}

	public function remove_user($user_id) {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		if ( !$this->is_user_admin() ) redirect(base_url() , 'refresh');
		$this->load->model('User');
		$this->User->remove_user($user_id);
		redirect(base_url() . "/index.php/admin/teachers" , "refresh");
	}

	public function export($type) {
		if ( !$this->session_started() ) redirect(base_url() , 'refresh');		
		if ( !$this->is_user_admin() ) redirect(base_url() , 'refresh');
		if ($type == 0) {
			header('Content-disposition: attachment; filename=students.txt');
			header('Content-type: text/plain');
			$this->load->model('Student');
			$students = $this->Student->get_all_students();
			foreach($students->result() as $row) {
				echo $row->NAME . "  |  " . $row->LOGIN . "  |  " . $row->GRADE . "  |  " . $row->PASSWORD . "\r\n";
			}
		} else if ($type == 1) {
			header('Content-disposition: attachment; filename=teachers.txt');
			header('Content-type: text/plain');
			$this->load->model('User');
			$teachers = $this->User->get_all_users();
			foreach($teachers->result() as $row) {
				echo $row->name . "  |  " . $row->login . "  |  " . $row->password . "\r\n";
			}
		}
	}

}
