
const SAVE_ANSWERS_INTERVAL = 1 * 60 * 1000;

var save_info_interval;
var answers;
var total_questions;

$(document).ready(function() {		
	
	answers = [];
	load_info();	

	$("#finish").click(function() {				
		var n_a = $("input:radio:checked").length;
		var n_q = $("div.panel-heading").length;
		if (n_a == n_q) {
			clearInterval(save_info_interval);
		}
	})

	$("#q_answered").hcSticky({
		top: 70,
	});

	var answered_cnt = $("input:radio:checked").length;
	total_questions = $("input:radio").length / 5;
	$("#q_answered_cnt").html(answered_cnt + "/" + total_questions);
	
	$("#error").jrumble({
		x: 2,
		y: 2,
		rotation: 1
	});

});

// Загрузка сохраненных ответов и проставление
function load_info() {
	var URL = get_root() + '/ajax/load';		
	$.ajax({
		type: "POST",
		url: URL,
		dataType: 'json',
		success: function(data) {
			// $("#messages").append("Success, but " + JSON.stringify(data));
			$.each(data , function(i) {
				if (data[i] != '') {
					var page_answer = $('[value=' + data[i] + ']');
					page_answer.attr('checked' , true);
					var needed_parent = page_answer.parent().parent().parent().parent();
					needed_parent.attr('value' , 'true');
				}
			});
			update_answered();
			save_info_interval = setInterval(save_info , SAVE_ANSWERS_INTERVAL);
		},
		error: function(text) {				
			console.log("Errors on load :C");
		}
	});
}

// Сохранение выбранных ответов
function save_info() {
	var URL = get_root() + '/ajax/save';		
	var answered = $("input:radio:checked");
	for (var i = 0 ; i < answered.length ; i++) {		
		var answer_id = answered[i]['value'];
		answers[i] = answer_id;
	}
	$.ajax({
		type: "POST",
		url: URL,
		data: {'answers_ajax' : JSON.stringify(answers)},
		dataType: 'json',	
		success: function(data) {
		},
		error: function(data) {
			console.log("Error on save :C");
		}
	});
}

// Функция получения корневого адреса
function get_root() {
	var root = window.location.toString();
	var ps = root.indexOf('index.php') + 9;
	root = root.substring(0 , ps);
	return root;
}

// Обновление количества отвеченных вопросов
function update_answered(n_question) {
	var answered_cnt = $("input:radio:checked").length;
	$("#q_answered_cnt").html(answered_cnt + "/" + total_questions);
	var q_flag = $("#question_flag_" + n_question);
	q_flag.attr('value' , 'true');
}

// Нажатие на поиск неотвеченных вопросов
$("#search").click(function() {
	var not_answered = $('[value=false]')
	if (not_answered.length == 0) {	
		var bottom = $("body").height();
		doAnimation(bottom, 'finish');		
	} else {
		var first_question_id = not_answered[0];		
		first_question_id = first_question_id.id;
		var destination = $("#" + first_question_id).offset().top - 70;
		var result = $("#" + first_question_id).attr('value');
		doAnimation(destination, first_question_id);		
	}
});

// Небольшая анимация
function doAnimation(destination, id) {
	$("#" + id).jrumble({
		x: 1,
		y: 1,
		rotation: 1
	});
	$("body,html").animate({scrollTop: destination}, 1000, function() {
		var rumbleTimeout;
		clearTimeout(rumbleTimeout);
		$("#" + id).trigger('startRumble');
		rumbleTimeout = setTimeout(function() {$("#" + id).trigger('stopRumble');}, 200)
	});
}
