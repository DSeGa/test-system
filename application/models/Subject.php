<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subject extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	// Метод получения информации о предметах, по которым есть экзамены
	public function get_subjects() {				
		$s_left_tests = $this->get_available_ids();
		if ($s_left_tests == "()")
			return $this->empty_result();
		return $this->db->query("SELECT SUBJECT.ID as ID , SUBJECT.SUBJECT_NAME as NAME, COUNT(TESTS.ID) as NUMBEROFTESTS FROM TESTS LEFT JOIN SUBJECT ON TESTS.ID_SUBJECT = SUBJECT.ID WHERE TESTS.ID IN $s_left_tests AND TESTS.IS_AVAILABLE = 1 GROUP BY SUBJECT.ID");
	}


	public function get_tests() {
		$s_left_tests = $this->get_available_ids();
		if ($s_left_tests == "()") 
			return $this->empty_result();
		return $this->db->query("SELECT TESTS.ID , TESTS.SECTION , TESTS.ID_SUBJECT , TESTS.NUM_QUESTIONS FROM TESTS WHERE ID IN $s_left_tests AND IS_AVAILABLE = 1");
	}


	private function empty_result() {
		return $this->db->query("SELECT ID FROM TESTS WHERE ID = 0");
	}


	// Получаем допустимые ID тестов
	private function get_available_ids() {
		$student_id = $this->session->student_id;
		$student_grade = $this->db->query("SELECT GRADE FROM STUDENTS WHERE ID = $student_id")->row()->GRADE;

		// Получаем все тесты, которые может пройти этот ученик
		$q_all_tests = $this->db->query("SELECT ID FROM TESTS WHERE GRADE = $student_grade");
		$all_tests = array();
		foreach ($q_all_tests->result() as $row) $all_tests[] = $row->ID;
		
		// Получаем тесты, которые уже были отвечены
		$q_done_tests = $this->db->query("SELECT ID_TEST FROM RESULTS WHERE ID_STUDENT = $student_id");
		$done_tests = array();
		foreach ($q_done_tests->result() as $row) $done_tests[] = $row->ID_TEST;

		// Получаем тесты, которые не были отвечены
		$left_tests = array_diff($all_tests, $done_tests);
		$s_left_tests = "";
		foreach ($left_tests as $one_test) $s_left_tests .= $one_test . ",";
		$s_left_tests = substr_replace($s_left_tests , '' , strlen($s_left_tests) - 1 , 1);
		$s_left_tests = "(" . $s_left_tests . ")";

		return $s_left_tests;
	}


}

?>