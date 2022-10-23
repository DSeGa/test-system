<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = "container">
	<div class = "row" style = "margin-bottom: 10px;">
		<div class="col-md-12">
			<a class = "btn btn-sm text-right btn-success pull-right" href = "<?php echo base_url();?>/index.php/admin/export/1" target = "_blank">Экспорт в txt</a>		
		</div>		
	</div>
	<div class = "table-responsive">
		<table class = "table table-condensed">
			<thead>
				<tr>
					<th>#</th>
					<th>Предмет</th>
					<th>Логин</th>
					<th>Пароль</th>
					<th class = "text-right">Действие</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$r = 0;
				foreach ($users->result() as $row) {
					$r++;
					echo "<tr>";
					echo "<td>$r</td>";
					echo "<td>" . $row->name . "</td>";
					echo "<td>" . $row->login . "</td>";
					echo "<td>" . $row->password . "</td>";
					echo "<td class = 'text-right'><form action = '" . base_url() . "/index.php/admin/remove_user/$row->id' method = 'post'><button type = 'submit' class = 'btn btn-danger btn-xs'>Удалить</button></form></td>"; 
					echo "</tr>";
				}
			?>							
			</tbody>
		</table>
	</div>


	<form action = "<?php echo base_url()?>index.php/admin/add_new_user" method = "POST" role = "form">
		<div class="row">
			<div class = "col-md-5">
				<input type="text" class="form-control" placeholder="Предмет" name = "subject_name">			
			</div>	
			<div class = "col-md-5">
				<input type="text" class="form-control" placeholder="Логин" name = "login">			
			</div>	
			<div class="col-md-2 text-right">
				<button type = "submit" class = "btn btn-default">Добавить</button>		
			</div>
		</div>		
	</form>

</div>

