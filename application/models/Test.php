<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_questions($test_id) {
		// Проверка, чтобы студент сдавал только экзамен из списка доступных
		$available = $this->get_available_ids();
		if ( !in_array($test_id, $available) ) {
			// Попытка взлома :)
			$this->load->model('Student');
			$this->Student->do_logout();
		}

		// Получаем сами вопросы
		$questions = $this->db->query("SELECT ID , TEXT FROM QUESTIONS WHERE ID_TEST = $test_id ORDER BY RAND()");
		$this->db->select("*");
		$this->db->from("ANSWERS");
		$ok = false;
		foreach ($questions->result() as $row) {
			if (!$ok) {
				$this->db->where("ID_QUESTION" , $row->ID);
				$ok = true;
			} else {
				$this->db->or_where("ID_QUESTION" , $row->ID);
			}
		}
		$this->db->order_by('ID' , 'RANDOM');
		$data['answers'] = $this->db->get();
		$data['questions'] = $questions;

		// Студент начал экзамен, отмечаем этот факт в БД.
		$student_id = $this->session->student_id;
		$this->db->select('ID');
		$this->db->where(array('ID_STUDENT' => $student_id));
		$this->db->from('TEMP_ANSWERS');		
		if ($this->db->get()->row() == NULL) {
			$insert_data = array(
				'ID_STUDENT' => $this->session->student_id,
				'ID_TEST' => $test_id
				);		
			$this->db->insert('TEMP_ANSWERS' , $insert_data);
		}

		return $data;
	}

	// Проверка теста и добавление результатов в таблицу
	public function check_test($test_id) {
		$questions = $this->db->query("SELECT ID FROM QUESTIONS WHERE ID_TEST = $test_id");
		$student_id = $this->session->student_id;
		$right = 0;
		$question_ids = array();
		$count = 0;
		foreach ($questions->result() as $row) {
			$question_ids[] = $row->ID;
			$count++;
		}
		$questions->free_result();
		for ($i = 0 ; $i < $count ; $i++) {
			$question_id = $question_ids[$i];
			$student_answer = $_POST['question_' . $question_id];			
			$true_answer = $this->db->query("SELECT ID_ANSWER FROM QUESTIONS WHERE ID = $question_id")->row()->ID_ANSWER;
			$data = array(
				'ID_STUDENT' => $student_id,
				'ID_ANSWER' => $student_answer,
				'ID_QUESTION' => $question_id
				);

			$this->db->insert('STUDENT_ANSWER' , $data);
			if ($student_answer == $true_answer) $right++;
		}
		$data = array(
				'ID_STUDENT' => $student_id,
				'ID_TEST' => $test_id,
				'POINTS' => $right,
			);
		$this->db->insert('RESULTS' , $data);

		//	Удалить временные ответы
		$this->db->where('ID_STUDENT' , $student_id);
		$this->db->delete('TEMP_ANSWERS');

		return $this->db->query("SELECT ID FROM RESULTS WHERE ID_TEST = $test_id AND ID_STUDENT = $student_id")->row()->ID;
	}

	// Загрузка данных об уже сданных тестах
	public function load_history() {
		$student_id = $this->session->student_id;
		// Сначала загрузим те предметы, по которым уже есть сданные тесты
		$return['subjects'] = $this->db->query("SELECT SUBJECT.ID , SUBJECT.SUBJECT_NAME FROM SUBJECT WHERE ID IN (SELECT TESTS.ID_SUBJECT FROM TESTS WHERE ID IN (SELECT RESULTS.ID_TEST FROM RESULTS WHERE ID_STUDENT = $student_id))");
		$return['tests'] = $this->db->query("SELECT RESULTS.ID , TESTS.SECTION , TESTS.ID_SUBJECT , TESTS.NUM_QUESTIONS , RESULTS.POINTS FROM RESULTS LEFT JOIN TESTS ON RESULTS.ID_TEST = TESTS.ID WHERE RESULTS.ID_STUDENT = $student_id");
		return $return;
	}


	public function get_result($result_id) {
		$student_id = $this->session->student_id;
		// А вдруг другой студент пытается смотреть не свои результаты?
		if ($student_id != $this->db->query("SELECT ID_STUDENT FROM RESULTS WHERE ID = $result_id")->row()->ID_STUDENT) {
			$this->load->model('Student');
			$this->Student->do_logout();
		}
		$test_id = $this->db->query("SELECT ID_TEST FROM RESULTS WHERE ID = $result_id")->row()->ID_TEST;
		$return['student_answers'] = $this->db->query("SELECT ID_ANSWER FROM STUDENT_ANSWER WHERE ID_STUDENT = $student_id AND ID_QUESTION IN (SELECT ID FROM QUESTIONS WHERE ID_TEST = $test_id)");
		$return['questions'] = $this->db->query("SELECT ID , TEXT , ID_ANSWER FROM QUESTIONS WHERE ID_TEST = $test_id");

		// Выбираем все ответы для этих тестов
		$this->db->select("*");
		$this->db->from("ANSWERS");
		$ok = false;
		foreach ($return['questions']->result() as $row) {
			if (!$ok) {
				$this->db->where("ID_QUESTION" , $row->ID);
				$ok = true;
			} else {
				$this->db->or_where("ID_QUESTION" , $row->ID);
			}
		}
		$return['answers'] = $this->db->get();
		$return['total'] = $this->db->query("SELECT POINTS FROM RESULTS WHERE ID = $result_id")->row()->POINTS;
		$return['num_q'] = $this->db->query("SELECT NUM_QUESTIONS FROM TESTS WHERE ID = $test_id")->row()->NUM_QUESTIONS;
		$return['finish_date'] = $this->db->query("SELECT TEST_DATE FROM RESULTS WHERE ID = $result_id")->row()->TEST_DATE;
		return $return;
	}

	// Получить результат от АДМИНА
	public function get_result_admin($result_id) {
		// проверка, чтобы учитель смотрел результаты только по своему предмету
		$test_id = $this->db->query("SELECT ID_TEST FROM RESULTS WHERE ID = $result_id")->row()->ID_TEST;
		$subject_id = $this->db->query("SELECT ID_SUBJECT FROM TESTS WHERE ID = $test_id")->row()->ID_SUBJECT;
		if ($subject_id != $this->session->subject_id) {
			$this->load->model('User');
			$this->User->do_logout();
		}
		
		$student_id = $this->db->query("SELECT ID_STUDENT FROM RESULTS WHERE ID = $result_id")->row()->ID_STUDENT;
		$return['student_answers'] = $this->db->query("SELECT ID_ANSWER FROM STUDENT_ANSWER WHERE ID_STUDENT = $student_id AND ID_QUESTION IN (SELECT ID FROM QUESTIONS WHERE ID_TEST = $test_id)");
		$return['questions'] = $this->db->query("SELECT ID , TEXT , ID_ANSWER FROM QUESTIONS WHERE ID_TEST = $test_id");

		// Выбираем все ответы для этих тестов
		$this->db->select("*");
		$this->db->from("ANSWERS");
		$ok = false;
		foreach ($return['questions']->result() as $row) {
			if (!$ok) {
				$this->db->where("ID_QUESTION" , $row->ID);
				$ok = true;
			} else {
				$this->db->or_where("ID_QUESTION" , $row->ID);
			}
		}
		$return['answers'] = $this->db->get();
		$return['total'] = $this->db->query("SELECT POINTS FROM RESULTS WHERE ID = $result_id")->row()->POINTS;
		$return['num_q'] = $this->db->query("SELECT NUM_QUESTIONS FROM TESTS WHERE ID = $test_id")->row()->NUM_QUESTIONS;
		$return['finish_date'] = $this->db->query("SELECT TEST_DATE FROM RESULTS WHERE ID = $result_id")->row()->TEST_DATE;
		return $return;
	}


	// Получаем допустимые ID тестов
	private function get_available_ids() {
		$student_id = $this->session->student_id;

		// Получаем все тесты
		$q_all_tests = $this->db->query("SELECT ID FROM TESTS");
		$all_tests = array();
		foreach ($q_all_tests->result() as $row) $all_tests[] = $row->ID;
		
		// Получаем тесты, которые уже были отвечены
		$q_done_tests = $this->db->query("SELECT ID_TEST FROM RESULTS WHERE ID_STUDENT = $student_id");
		$done_tests = array();
		foreach ($q_done_tests->result() as $row) $done_tests[] = $row->ID_TEST;

		// Получаем тесты, которые не были отвечены
		$left_tests = array_diff($all_tests, $done_tests);
		
		return $left_tests;
	}



	public function add_new_test() {
		$subject_id = $this->session->subject_id;
		$section = $_POST['section_name'];
		$grade = $_POST['section_grade'];
		$data = array(
				'ID_SUBJECT' => $subject_id,
				'SECTION' => $section,
				'NUM_QUESTIONS' => 0,
				'GRADE' => $grade
			);
		$this->db->insert('TESTS' , $data);		
		$test_id = $this->db->query("SELECT ID FROM TESTS WHERE ID_SUBJECT = $subject_id AND SECTION = '$section'")->row()->ID;
		$this->session->set_userdata('test_id' , $test_id);
	}


	// Сохранение нового вопроса
	public function save_question() {
		$this->load->library('upload');
		$BIG = 1000000;
		$test_id = $this->session->test_id;
		$question = $_POST['question'];

		// Замена <p></p> на <br>, и удаление оставшихся <p>
		$question = str_replace("</p><p>", "<br>", $question);
		$question = str_replace("<p>" , "" , $question);
		$question = str_replace("</p>" , "" , $question);

		$data = array(
				'TEXT' => $question,
				'ID_TEST' => $test_id,
				'ID_ANSWER' => $BIG
			);
		$this->db->insert('QUESTIONS' , $data);

		$data = array(
			'ID_TEST' => $test_id,
			'TEXT' => $question
			);
		$this->db->select('ID');
		$this->db->where($data);
		$question_id = $this->db->get('QUESTIONS')->row()->ID;

		if (isset($_FILES['question_file']) && $_FILES['question_file']['size'] > 0) {
			$question .= "<br><img src = '" . $this->do_upload_question($question_id) . "' />";
			$this->db->set('TEXT' , $question);
			$this->db->where('ID' , $question_id);
			$this->db->update('QUESTIONS');
		}

		$real_answer = $_POST['new_question'];
		$answer_id = -1;
		for ($i = 1 ; $i <= 5 ; $i++) {
			// Первоначальное добавление ответа.
			$answer = $_POST['ans_' . $i];
			$data = array(
				'TEXT' => $answer,
				'ID_QUESTION' => $question_id
				);
			$this->db->insert('ANSWERS' , $data);

			// Вычисляем ID добавленного ответа
			$find_data = array(
				'TEXT' => $answer,
				'ID_QUESTION' => $question_id
				);
			$this->db->select('ID');
			$this->db->where($find_data);
			$temp_answer_id = $this->db->get('ANSWERS')->row()->ID;

			// Если к вопросу идет картинка, сохраняем её.
			if (isset($_FILES['answer_file_' . $i]) && $_FILES['answer_file_' . $i]['size'] > 0) {
				$answer .= "<br><img src = '" . $this->do_upload_answer($temp_answer_id , $i) . "' />";
				$this->db->set('TEXT' , $answer);
				$this->db->where('ID' , $temp_answer_id);
				$this->db->update('ANSWERS');
			}

			// Нашли ответ, сохраним его.
			if ($real_answer == $i) $answer_id = $temp_answer_id;	
		}

		$this->db->query("UPDATE QUESTIONS SET ID_ANSWER = $answer_id WHERE ID = $question_id");
		$this->db->query("UPDATE TESTS SET NUM_QUESTIONS = NUM_QUESTIONS + 1 WHERE ID = $test_id");
	}

	// Получение раздела по ID
	public function load_section($test_id) {
		return $this->db->query("SELECT SECTION FROM TESTS WHERE ID = $test_id")->row()->SECTION;
	}

	// Получение количества вопросов для теста
	public function load_question_amount($test_id) {
		return $this->db->query("SELECT ID FROM QUESTIONS WHERE ID_TEST = $test_id")->num_rows();
	}


	public function save_test() {
		$test_id = $this->session->test_id;
		$this->session->unset_userdata($test_id);
		$this->db->query("UPDATE TESTS SET IS_AVAILABLE = 1 WHERE ID = $test_id");
	}

	public function get_section_by_id($test_id) {
		return $this->db->query("SELECT SECTION FROM TESTS WHERE ID = $test_id")->row()->SECTION;
	}

	public function get_total_questions($test_id) {
		return $this->db->query("SELECT NUM_QUESTIONS FROM TESTS WHERE ID = $test_id")->row()->NUM_QUESTIONS;
	}

	public function get_results($test_id) {
		return $this->db->query("SELECT STUDENTS.NAME , RESULTS.POINTS FROM RESULTS LEFT JOIN STUDENTS ON RESULTS.ID_STUDENT = STUDENTS.ID WHERE RESULTS.ID_TEST = $test_id");
	}


	// Получение списка тестов
	public function get_tests_for_editing() {
		$subject_id = $this->session->subject_id;
		$this->db->select('ID, SECTION');
		$this->db->where('ID_SUBJECT' , $subject_id);
		// $this->db->where('is_available' , 1);
		return $this->db->get('TESTS');
	}


	// Загрузка картинки для вопроса
	public function do_upload_question($question_id) {	
		$config = $this->get_init('q' . $question_id);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('question_file')) {
		} else {
			$return_value = base_url() . 'assets/images/pics/q' . $question_id . $this->upload->data('file_ext');
			return $return_value;
		}
	}

	// Загрузка картинки для ответа
	public function do_upload_answer($answer_id, $order_id) {
		$config = $this->get_init('a' . $answer_id);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('answer_file_' . $order_id)) {
		} else {			
			$return_value = base_url() . 'assets/images/pics/a' . $answer_id . $this->upload->data('file_ext');
			log_message('error' , $return_value);
			return $return_value;
		}	
	}


	public function get_init($param) {
		$config['upload_path'] = './assets/images/pics/';
		$config['allowed_types'] = 'jpg|png|bmp|jpeg';
		$config['max_size'] = 1024;
		$config['max_width'] = 1024;
		$config['max_height'] = 768;
		$config['file_name'] = $param;
		return $config;
	}

}

?>