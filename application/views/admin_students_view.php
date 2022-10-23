<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = "container">
	<div class = "row" style = "margin-bottom: 10px;">
		<div class="col-md-12">
			<a class = "btn btn-sm text-right btn-success pull-right" href = "<?php echo base_url();?>/index.php/admin/export/0" target = "_blank">Экспорт в txt</a>		
		</div>		
	</div>
	
	<div class = "table-responsive">
		<table class = "table table-condensed">
			<thead>
				<tr>
					<th>#</th>
					<th>ФИО</th>
					<th>Класс</th>
					<th>Логин</th>
					<th>Пароль</th>
					<th class = "text-right">Действие</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$r = 0;
				foreach ($students->result() as $row) {
					$r++;
					echo "<tr>";
					echo "<td>$r</td>";
					echo "<td>" . $row->NAME . "</td>";
					echo "<td>" . $row->GRADE . "</td>";
					echo "<td>" . $row->LOGIN . "</td>";
					echo "<td>" . $row->PASSWORD . "</td>";
					echo "<td class = 'text-right'><form action = '" . base_url() . "/index.php/admin/remove_student/$row->ID' method = 'post'><button type = 'submit' class = 'btn btn-danger btn-xs'>Удалить</button></form></td>"; 
					echo "</tr>";
				}
			?>							
			</tbody>
		</table>
	</div>


	<form action = "<?php echo base_url()?>index.php/admin/add_new_student" method = "POST" role = "form">
		<div class="row">
			<div class = "col-md-8">
				<input type="text" class="form-control" placeholder="Фамилия Имя" name = "fio">			
			</div>	
			<div class = "col-md-2">
				<input type="text" class="form-control" placeholder="Класс" name = "grade">			
			</div>	
			<div class="col-md-2 text-right">
				<button type = "submit" class = "btn btn-default">Добавить</button>		
			</div>
		</div>		
	</form>

</div>
<script src="<?php echo base_url()."assets/js"?>/export_util.js" type = 'text/javascript'></script>	

