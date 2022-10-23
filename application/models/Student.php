<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends CI_Model {

	public $alpha = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM7410852963";	
	public $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
	public $lat = [
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
        ];

	public $LOGIN_ERROR = 1;
	public $LOGIN_SUCCESS = 0;

	public function __construct() {
		parent::__construct();
	}

	// Попытка войти в систему
	public function do_login() {
		$login = $_POST['login'];
		$password = $_POST['password'];

		$this->db->from('STUDENTS');
		$this->db->where(array('login' => $login, 'password' => $password));
		$query = $this->db->get();

		if ($query->num_rows() != 1) {
			// Ошибка при входе
			$this->session->set_userdata('login_error' , '+');
			return $this->LOGIN_ERROR;
		} 

		// Иначе, все ОК, вход выполнен
		$row = $query->row();
		$this->session->set_userdata('student_id' , $row->ID);
		return $this->LOGIN_SUCCESS;
	}


	// Выход из системы - завершение сессии
	public function do_logout() {
		$this->session->sess_destroy();
		redirect('' , 'refresh');
	}


	// Получение имени текущего студента сессии
	public function get_name() {
		$student_id = $this->session->student_id;
		$this->db->from('STUDENTS');
		$this->db->select('NAME');
		$this->db->where(array('ID' => $student_id));
		return $this->db->get()->row()->NAME;
	}


	// Проверка, запущен ли для текущего студента уже экзамен
	public function has_exam() {
		$student_id = $this->session->student_id;
		$this->db->select('ID');
		$this->db->where(array('ID_STUDENT' => $student_id));
		$this->db->from('TEMP_ANSWERS');
		return ($this->db->get()->row() != NULL);
	}


	// Получение экзамена, который сейчас сдает студент
	public function get_current_test_id() {
		$student_id = $this->session->student_id;
		$this->db->select('ID_TEST');
		$this->db->where(array('ID_STUDENT' => $student_id));
		$this->db->from('TEMP_ANSWERS');
		return ($this->db->get()->row()->ID_TEST);	
	}

	public function get_all_students() {
		$this->db->select("*");
		$this->db->from("STUDENTS");
		return $this->db->get();
	}

	public function add_new_student($fio , $grade) {	
		$fio2 = mb_strtolower($fio);
		$password = "";
		for ($i = 0 ; $i < 6 ; $i++) {
			$password .= $this->alpha[rand(0, mb_strlen($this->alpha) - 1)];
		}
		$login = str_replace($this->cyr, $this->lat, $fio2);
		$login_parts = explode(" " , $login);
		$login = $login_parts[0] . "." . $login_parts[1][0];
		$ins = array(
			'LOGIN' => $login,
			'PASSWORD' => $password,
			'NAME' => $fio,
			'GRADE' => $grade
		);
		$this->db->insert('STUDENTS' , $ins);
	}

	public function remove_student($student_id) {
		$this->db->where('ID' , $student_id);
		$this->db->delete('STUDENTS');
		// Also we need to remove all data of this student
	}

}

?>