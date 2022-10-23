function formFilled() {

	
	// Проверка, что введен вопрос
	var q = $("input#question");
	if (q == null || q == undefined || q.val() == "") {
		$("#error-message").text("Ошибка! Вы не ввели вопрос... А если ве же ввели, то просто нажмите на окно ввода вопроса и попробуйте еще раз сохранить.");
		$("div#error").removeClass("hidden");
		q.focus();
		return false;
	}

	// Проверка, что введены ответы
	for (var i = 0 ; i < 5 ; i++) {
		var a = $("input#ans_" + i).val();
		if (a == null || a == undefined || a == "") {
			$("#error-message").text("Ошибка! Вы не ввели вариант ответа #" + (i + 1));
			$("div#error").removeClass("hidden");
			$("input#ans_" + i).focus();
			return false;
		}
	}

	// Проверка, что выбран ответ
	var ans = $("input:radio:checked").val();
	if (ans == null || ans == undefined) {
		$("#error-message").text("Ошибка! Вы не выбрали правильный ответ...");
		$("div#error").removeClass("hidden");
		return false;	
	}
	return true;
}


function checkForm() {
	// Кол-во выбранных должно быть равным количеству вопросов!
	var answered = $("input:radio:checked").length;
	var questions = $("div.panel-default").length;
	if (answered != questions) {
		$("#error-message").text("Ошибка! Вы не ответили на все вопросы...");
		$("div#error").removeClass("hidden");
		$("body,html").animate({scrollTop: 0}, 1000, function() {
			var rumbleTimeout;
			clearTimeout(rumbleTimeout);
			$("#error").trigger('startRumble');
			rumbleTimeout = setTimeout(function() {$("#error").trigger('stopRumble');}, 2000)
		});
		return false;
	}
	return true;
}


