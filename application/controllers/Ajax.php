<?php

	defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function _remap($method , $params = array()) {
		if ($method == 'load') {
			$this->$method();
		} else if ($method == 'save') {
			$this->$method();
		} else if ($method == 'get_shortened_questions') {
			$this->$method();
		} else if ($method == 'get_full_question') {
			$this->$method();
		} else if ($method == 'update_question') {
			$this->$method();
		} else if ($method == 'delete_question') {
			$this->$method();
		} else if ($method == 'close_test') {
			$this->$method();
		} else if ($method == 'open_test') {
			$this->$method();
		} else if ($method == 'get_test_state') {
			$this->$method();
		} else if ($method == 'download_students') {
			$this->$method();
		} else {
			$this->wrong_params();
		}
	}


	// Функция получения уже отвеченных вопросов
	public function load() {
		if (!$this->session_started()) return;
		$student_id = $this->session->student_id;
		$this->db->select('CURRENT_ANSWERS');
		$this->db->from('TEMP_ANSWERS');
		$this->db->where('ID_STUDENT' , $student_id);
		$data = $this->db->get()->row()->CURRENT_ANSWERS;
		$return = explode(":" , $data);
		echo json_encode($return);
	}

	// Функция сохранения текущих ответов тестируемого
	public function save() {
		if (!$this->session_started()) return;
		if (isset($_POST) && !empty($_POST['answers_ajax'])) {
			$answers = json_decode($_POST['answers_ajax']);
			$data = "";
			for ($i = 0 ; $i < count($answers) ; $i++) {
				$data .= $answers[$i];
				if ($i < count($answers) - 1) $data .= ":";
			}			
			$student_id = $this->session->student_id;
			$this->db->set('CURRENT_ANSWERS' , $data);
			$this->db->where('ID_STUDENT' , $student_id);
			$this->db->update('TEMP_ANSWERS');			
			echo json_encode("Ok");
		} else {
			echo json_encode("Empty");
		}		
	}

	// Метод для полчуения всех вопросов теста 
	public function get_shortened_questions() {
		if (!$this->session_started()) return;
		if (isset($_POST['test_id'])) {
			$test_id = $_POST['test_id'];
			$this->db->select('ID, TEXT');
			$this->db->where('ID_TEST' , $test_id);
			$query = $this->db->get('QUESTIONS');
			$return = array();
			foreach ($query->result() as $row) {
				$text = $row->TEXT;
				if (mb_strlen($text) > 130) $text = mb_substr($row->TEXT, 0, 128) . "...";
				$object = array($row->ID, $text);
				array_push($return , $object);
			}
			echo json_encode($return);
		} else {			
		}				
	}

	public function get_test_state() {
		if (!$this->session_started()) return;
		if (isset($_POST['test_id'])) {
			$test_id = $_POST['test_id'];
			$this->db->select('IS_AVAILABLE');
			$this->db->where('ID' , $test_id);
			$query = $this->db->get('TESTS');
			$ret = "";
			foreach ($query->result() as $row) {
				$ret = $row->IS_AVAILABLE;
			}
			echo json_encode($ret);
		}
	}

	// Метод для получения всей информации о вопросе
	public function get_full_question() {
		if (!$this->session_started()) return;
		if (isset($_POST['question_id'])) {
			$question_id = $_POST['question_id'];
			$this->db->select('TEXT, ID_ANSWER');
			$this->db->where('ID' , $question_id);
			$query = $this->db->get('QUESTIONS')->row();
			$question = array(
				'TEXT' => $query->TEXT,
				'ID_ANSWER' =>$query->ID_ANSWER
				);
			$return = array();
			array_push($return , $question);

			$this->db->select('ID, TEXT');
			$this->db->where('ID_QUESTION' , $question_id);
			$query = $this->db->get('ANSWERS');

			foreach ($query->result() as $row) {
				$answer = array($row->ID , $row->TEXT);
				array_push($return , $answer);
			}
			echo json_encode($return);
		} else {			
		}				
	}


	// Функция обновления информации о вопросе
	public function update_question() {
		if (!$this->session_started()) return;
		if (isset($_POST['question_id'])) {
			$question_id = $_POST['question_id'];
			$question_text = $_POST['question_text'];
			$question_text = str_replace("</p><p>", "<br>", $question_text);
			$question_text = str_replace("<p>" , "" , $question_text);
			$question_text = str_replace("</p>" , "" , $question_text);
			$answers = json_decode($_POST['answers']);

			$this->db->set('TEXT' , $question_text);
			$this->db->where('ID' , $question_id);
			$this->db->update('QUESTIONS');

			$this->db->select('ID');			
			$this->db->from('ANSWERS');
			$this->db->where('ID_QUESTION' , $question_id);
			$answer_ids = $this->db->get();

			$i = 0;
			foreach ($answer_ids->result() as $row) {
				$answer_id = $row->ID;
				$answer_text = $answers[$i];
				$i++;

				$this->db->set('TEXT' , $answer_text);
				$this->db->where('ID' , $answer_id);
				$this->db->update('ANSWERS');
			}
			echo json_encode("Вопрос успешно обновлен.");
		} else {			
		}
	}


	// Функция удаления вопроса
	public function delete_question() {
		if (!$this->session_started()) return;
		if (isset($_POST['question_id'])) {
			$question_id = $_POST['question_id'];

			// Получаем номер теста
			$this->db->select('ID_TEST');
			$this->db->from('QUESTIONS');
			$this->db->where('ID' , $question_id);
			$test_id = $this->db->get()->row()->ID_TEST;

			// Удаляем вопрос из таблицы QUESTIONS
			$this->db->where('ID' , $question_id);
			$this->db->delete('QUESTIONS');

			// Уменьшается количество вопросов в тесте
			$this->db->query("UPDATE TESTS SET NUM_QUESTIONS = NUM_QUESTIONS - 1 WHERE ID = $test_id");
			$num = $this->db->query("SELECT NUM_QUESTIONS FROM TESTS WHERE ID = $test_id")->row()->NUM_QUESTIONS;
			if ($num == 0) {
				$this->db->query("UPDATE TESTS SET is_available = 0 WHERE ID = $test_id");
			}

			// Удаляем ответы на этот вопрос
			$this->db->where('ID_QUESTION' , $question_id);
			$this->db->delete('ANSWERS');

			// Удаляем ответы студентов на этот вопрос
			$this->db->where('ID_QUESTION' , $question_id);
			$this->db->delete('STUDENT_ANSWER');

			echo json_encode("Вопрос был удален");
		} else {			
		}
	}

	public function close_test() {
		if (!$this->session_started()) return;
		if (isset($_POST['test_id'])) {
			$test_id = $_POST['test_id'];
			$this->db->set('is_available' , 0);
			$this->db->where('ID' , $test_id);
			$this->db->update('TESTS');
			echo json_encode("Тест был успешно закрыт.");
		}
	}

	public function open_test() {
		if (!$this->session_started()) return;
		if (isset($_POST['test_id'])) {
			$test_id = $_POST['test_id'];
			$this->db->set('is_available' , 1);
			$this->db->where('ID' , $test_id);
			$this->db->update('TESTS');
			echo json_encode("Тест был успешно открыт.");
		}
	}


	public function wrong_params() {
		$this->load->view('error_view');
	}

	// Проверка, был ли произведен вход в систему
	private function session_started() {
		return (isset($_SESSION['user_id']));
	}

	public function download_students() {
		if (!$this->session_started()) return;
		$res = $this->db->query("SELECT LOGIN, PASSWORD, NAME FROM STUDENTS");
		$ret = "";
		foreach ($res->result() as $row) {
			$ret .= $row->LOGIN . " " . $row->PASSWORD . " " . $row->NAME . "\n"; 
		}
		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="report.txt"');
		print($output);
	}

}
