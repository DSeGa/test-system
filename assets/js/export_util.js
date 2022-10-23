
// Функция получения корневого адреса
function get_root() {
	var root = window.location.toString();
	var ps = root.indexOf('index.php') + 9;
	root = root.substring(0 , ps);
	return root;
}

// Нажата кнопка "Удалить вопрос"
$("#export").click(function() {
	var question_id = $("#select_question").val();
	var URL = get_root() + '/ajax/download_students';
	$.ajax({
		type: "POST",
		url: URL,
		dataType: 'json',
		success: function(data) {
			console.log('success');
		},
		error: function(data) {
			console.log('error');
		}
	});
});