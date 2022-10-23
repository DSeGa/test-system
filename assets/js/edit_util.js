

// Начальная загрузка данных
$(document).ready(function() {
	tinymce.init({
		selector: '#question',
		plugins: 'paste',
		menu: {},
		toolbar: 'undo redo | bold italic underline',
		force_br_newlines: true,
		paste_as_text: true
	});
	// При редактировании выбирется тест
	$("#select_test").change(function() {
		var URL = get_root() + '/ajax/get_shortened_questions';
		var test_id = $("#select_test").val();	
		$("input[type=text]").val("");
		$("input[type=radio]").prop("checked" , false);
		$("input[type=text]").prop("disabled" , "disabled");
		tinymce.get("question").setContent("");
		if (test_id == 0) {
			$("#select_question").prop("disabled" , "disabled");
			$("#select_question").empty();
			$("#update").prop("disabled" , "disabled");
			$("#delete").prop("disabled" , "disabled");
			$("#delete_all").prop("disabled" , "disabled");
			$("#add").prop("disabled" , "disabled");
			return;
		}
		var sendData = {
			'test_id' : test_id
		}
		$.ajax({
			type: 'post',
			url: URL,
			data: sendData,
			dataType: 'json',	
			success: function(data) {
				$("#select_question").empty();
				$("#select_question").append($('<option value = "0">Выберите вопрос...</option>'));
				var q_n = 1;
				$.each(data , function(index) {
					$("#select_question").append($('<option value = "' + data[index][0] + '"> Вопрос №' + q_n + '. ' + data[index][1] + '</option>'));
					q_n++;
				});
				$("#select_question").prop("disabled" , "");
				$("#add").prop("disabled" , "");
				$("#delete_all").prop("disabled" , "");
			},
			error: function(data) {
				console.log("Error on save :C");
				console.log(JSON.stringify(data));
			}
		});
		URL = get_root() + '/ajax/get_test_state';
		test_id = $("#select_test").val();
		$.ajax({
			type: 'POST',
			url: URL,
			data: {'test_id' : test_id},
			dataType: 'json',
			success: function(data) {
				if (data[0] == "1") {
					$("#delete_all").text("Закрыть тест");
					$("#delete_all").removeClass("btn-success");
					$("#delete_all").addClass("btn-danger");
				} else {
					$("#delete_all").text("Открыть тест");
					$("#delete_all").removeClass("btn-danger");
					$("#delete_all").addClass("btn-success");
				}
			},
			error: function(data) {
				console.log('error on getting state of test');
				console.log(data);
			}
		});
	});

	// Изменен вопрос
	$("#select_question").change(function() {
		var URL = get_root() + '/ajax/get_full_question';
		var question_id = $("#select_question").val();	
		if (question_id == 0) {
			do_init();		
			return;
		}
		$.ajax({
			type: "POST",
			url: URL,
			data: {'question_id' : question_id},
			dataType: 'json',	
			success: function(data) {									
				var answer_id = data[0]['ID_ANSWER'];
				tinymce.get("question").setContent(data[0]['TEXT']);
				for (var i = 1 ; i <= 5 ; i++) {
					$("#ans_" + (i - 1)).val(data[i][1]);
					if (data[i][0] === answer_id) {
						$("#new_question[value='" + i + "'").prop("checked" , true);
					}
				}
				$("input[type=text]").prop("disabled" , "");
				$("#update").prop("disabled" , "");
				$("#delete").prop("disabled" , "");
			},
			error: function(data) {
				console.log("Error on save :C");
				console.log(JSON.stringify(data));
			}
		});
	});

	// Нажата кнопка "Обновить вопрос"
	$("#update").click(function() {
		tinymce.triggerSave();
		var URL = get_root() + '/ajax/update_question';
		var question_id = $("#select_question").val();
		var question_text = $("#question").val();
		var test_id = $("select_test").val();
		var q_n = $("#select_question").prop('selectedIndex');
		var answers = [];
		for (var i = 0 ; i < 5 ; i++) answers[i] = $("#ans_" + i).val();
		
		$.ajax({
			type: "POST",
			url: URL,
			data: {'question_id' : question_id, 'question_text' : question_text, 'answers' : JSON.stringify(answers)},
			dataType: 'json',
			success: function(data) {
				$("#error-message").html(data);
				$("#error").removeClass("hidden");
				$("#error").removeClass("alert-danger");
				$("#error").removeClass("alert-success");
				$("#error").addClass("alert-success");	
				if (question_text.length > 130) question_text = question_text.substring(3 , 130) + '...'; else question_text = question_text.substring(3 , question_text.length - 4);
				$("#select_question option:selected").text('Вопрос №' + q_n + '. ' + question_text);
				go_top();
			},
			error: function(data) {
				$("#error-message").html("Ошибка. Что-то пошло не так...");
				$("#error").removeClass("alert-danger");
				$("#error").removeClass("alert-success");
				$("#error").addClass("alert-danger");
				go_top();
			}
		});
	});


	// Нажата кнопка "Удалить вопрос"
	$("#delete").click(function() {
		if ( !confirm("Вы действительно хотите удалить этот вопрос?")) {
			return;
		}
		var question_id = $("#select_question").val();
		var URL = get_root() + '/ajax/delete_question';
		$.ajax({
			type: "POST",
			url: URL,
			data: {'question_id' : question_id},
			dataType: 'json',
			success: function(data) {
				$("#error-message").html(data);
				$("#error").removeClass("hidden");
				$("#error").removeClass("alert-danger");
				$("#error").removeClass("alert-success");
				$("#error").addClass("alert-success");
				$("#select_question option:selected").remove();
				$("#select_question option[value='0']").select();
				// Здсь можно было бы обновить номера вопросов, чтобы сохранялся порядок
				var q_n = -1;
				var shift = 0;
				$("#select_question option").each(function() {
					q_n++;
					if (q_n == 9) shift++;
					var question_text = this.text;
					if (question_text != 'Выберите вопрос...') {
						question_text = "Вопрос №" + q_n + ". " + question_text.substring(10 + shift);
						this.text = question_text;
					}
				});
				do_init();
				go_top();
			},
			error: function(data) {
				$("#error-message").html("Ошибка. Что-то пошло не так...");
				$("#error").removeClass("alert-danger");
				$("#error").removeClass("alert-success");
				$("#error").addClass("alert-danger");
				go_top();
			}
		});
	});

	// Нажата кнопка удалить все
	$("#delete_all").click(function() {
		if ($("#delete_all").text() === "Закрыть тест") {
			if ( !confirm("Вы действительно хотите закрыть этот тест?")) {
				return;
			}
			var test_id = $("#select_test").val();
			var URL = get_root() + '/ajax/close_test';
			$.ajax({
				type: "POST",
				url: URL,
				data: {'test_id' : test_id},
				dataType: 'json',
				success: function(data) {
					location.reload();
				},
				error: function(data) {
					$("#error-message").html("Ошибка. Что-то пошло не так...");
					$("#error").removeClass("alert-danger");
					$("#error").removeClass("alert-success");
					$("#error").addClass("alert-danger");
				}
			});
		} else {
			if ( !confirm("Вы действительно хотите открыть этот тест?")) {
				return;
			}
			var test_id = $("#select_test").val();
			var URL = get_root() + '/ajax/open_test';
			$.ajax({
				type: "POST",
				url: URL,
				data: {'test_id' : test_id},
				dataType: 'json',
				success: function(data) {
					location.reload();
				},
				error: function(data) {
					$("#error-message").html("Ошибка. Что-то пошло не так...");
					$("#error").removeClass("alert-danger");
					$("#error").removeClass("alert-success");
					$("#error").addClass("alert-danger");
				}
			});
		}
	});

});


// Функция получения корневого адреса
function get_root() {
	var root = window.location.toString();
	var ps = root.indexOf('index.php') + 9;
	root = root.substring(0 , ps);
	return root;
}


function do_init() {
	$("input[type=text]").val("");
	$("input[type=text]").prop("disabled" , "disabled");
	$("#update").prop("disabled" , "disabled");
	$("#delete").prop("disabled" , "disabled");
	tinymce.get("question").setContent("");
	$("input[type=radio]").prop("checked" , false);
}

function go_top() {
	$("#error").jrumble({
		x: 1,
		y: 1,
		rotation: 1
	});
	$("body,html").animate({scrollTop: 0}, 200, function() {
		var rumbleTimeout;
		clearTimeout(rumbleTimeout);
		$("#error").trigger('startRumble');
		rumbleTimeout = setTimeout(function() {$("#error").trigger('stopRumble');}, 300)
	});
}