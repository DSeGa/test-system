<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

	public $alpha = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM7410852963";	

	public function __construct() {
		parent::__construct();
	}

	// Попытка войти в систему
	public function do_login() {
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$this->db->from('USERS');
		$this->db->where(array('login' => $login, 'password' => $password));
		$query = $this->db->get();

		if ($query->num_rows() != 1) {
			// Ошибка при входе
			$this->session->set_userdata('login_error' , '+');
			return 1;
		} 

		// Иначе, все ОК, вход выполнен
		$row = $query->row();
		$this->session->set_userdata('user_id' , $row->ID);
		$this->session->set_userdata('subject_id' , $row->SUBJECT_ID);
		return 0;
	}


	public function get_subject() {
		$user_id = $this->session->user_id;
		$subject_id = $this->session->subject_id;		
		return $this->db->query("SELECT SUBJECT_NAME FROM SUBJECT WHERE ID = $subject_id")->row()->SUBJECT_NAME;
	}


	// Выход из системы - завершение сессии
	public function do_logout() {
		$this->session->sess_destroy();
		redirect('' , 'refresh');
	}


	public function get_results() {
		$user_id = $this->session->user_id;
		$subject_id = $this->session->subject_id;
		$data['tests'] = $this->db->query("SELECT ID , SECTION , NUM_QUESTIONS , IS_AVAILABLE , GRADE FROM TESTS WHERE ID_SUBJECT = $subject_id");

		// Выбираем тесты, которые заданы по этому предмету
		$tests = array();
		foreach ($data['tests']->result() as $row) $tests[] = $row->ID;
		$s_tests = "";
		foreach ($tests as $row) $s_tests .= $row . ",";
		$s_tests = substr_replace($s_tests , '' , strlen($s_tests) - 1 , 1);
		$s_tests = "(" . $s_tests . ")";

		// Выбираем студентов, которые уже сдали необходимые экзамены
		if ($s_tests == "()") 
			$data['results'] = array();
		else					
			$data['results'] = $this->db->query("SELECT RESULTS.ID , RESULTS.ID_TEST , RESULTS.POINTS , STUDENTS.NAME FROM RESULTS LEFT JOIN STUDENTS ON RESULTS.ID_STUDENT = STUDENTS.ID WHERE ID_TEST IN $s_tests ORDER BY RESULTS.ID_TEST , STUDENTS.NAME");

		return $data;
	}

	public function get_lastname_by_result_id($result_id) {
		$student_id = $this->db->query("SELECT ID_STUDENT FROM RESULTS WHERE ID = $result_id")->row()->ID_STUDENT;
		return $this->db->query("SELECT NAME FROM STUDENTS WHERE ID = $student_id")->row()->NAME;
	}

	public function get_all_users() {
		return $this->db->query("SELECT USERS.ID as id, USERS.LOGIN as login, USERS.PASSWORD as password, SUBJECT.SUBJECT_NAME as name FROM users LEFT JOIN SUBJECT on USERS.SUBJECT_ID = SUBJECT.ID WHERE USERS.SUBJECT_ID > 0");
	}

	public function add_new_user($subject_name, $login) {	
		$password = "";
		for ($i = 0 ; $i < 6 ; $i++) {
			$password .= $this->alpha[rand(0, mb_strlen($this->alpha) - 1)];
		}
		$ins = array(
			'SUBJECT_NAME' => $subject_name
		);
		$this->db->insert('SUBJECT' , $ins);
		$subject_id = $this->db->insert_id();
		$ins = array(
			'LOGIN' => $login,
			'PASSWORD' => $password,
			'SUBJECT_ID' => $subject_id
		);
		$this->db->insert('USERS' , $ins);
	}

	public function remove_user($user_id) {
		$this->db->where('ID' , $user_id);
		$this->db->delete('USERS');
	}

}

?>