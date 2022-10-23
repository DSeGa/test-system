<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = 'container'>


	<div class = "panel panel-default">		
		<div class = "panel-heading" >
			<h4 class = "panel-title">
				Параметры теста
			</h4>
		</div>		
		<form action = "<?php echo base_url()?>index.php/admin/add_new_test" method = "POST" role = "form">				
			<div class = "panel-body" style = "padding-bottom: 0px;">
				<div class = "form-group">
					<label for = "section_name">Введите название теста:</label>
					<input type = 'text' class = 'form-control' id = 'section_name' name = 'section_name' placeholder = 'Введите тему'/>
				</div>				
				<div class = "form-group">
					<label for = "section_name">Выберите класс:</label>
					<select class = 'form-control' id = 'section_grade' name = 'section_grade'>
						<option value = "0">Выберите класс...</option>
						<option value = "1">1</option>
						<option value = "2">2</option>
						<option value = "3">3</option>
						<option value = "4">4</option>
						<option value = "5">5</option>
						<option value = "6">6</option>
						<option value = "7">7</option>
						<option value = "8">8</option>
						<option value = "9">9</option>
						<option value = "10">10</option>
						<option value = "11">11</option>
					</select>
				</div>
			</div>
			<input type = 'submit' name = 'submit' class = 'btn btn-primary pull-right add-test-offset' value = 'Добавить' />
		</form>		
	</div>

</div>
