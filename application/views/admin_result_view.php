<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = "container">
	<div class = "panel-group" id = "accordion" role = "tablist" aria-multiselectable = "true">
	<?php 
		foreach ($results['tests']->result() as $row) {
	?>
		<div class = "panel panel-default">
			
			<div class = "panel-heading" role = "tab" id = "<?php echo 'heading' . $row->ID; ?>">
				<h4 class = "panel-title">
					<a role = "button" data-toggle = "collapse" data-parent = "#accordion" href = "#collapse<?php echo $row->ID;?>" aria-expanded = "true" aria-controls = "collapse<?php echo $row->ID;?>">
						<?php echo $row->SECTION . ", " . $row->GRADE . " класс"; 
							echo "<span class = 'test_state'>";
							if ($row->IS_AVAILABLE == 1) echo " (Открыт)"; else echo " (Закрыт)"; 
							echo "</span>";
						?>

					</a>
					<a href = "<?php echo base_url()?>index.php/admin/do_report/<?php echo $row->ID;?>" class = "pull-right" target = "_blank">
						<img src = "<?php echo base_url()?>assets/images/excel-icon.png" alt = "Экспорт в Excel" style = "padding-top: 0; margin-top: -4px;">
					</a>
				</h4>
			</div>

			<div id = "collapse<?php echo $row->ID;?>" class = "panel-collapse collapse" role = "tabpanel" aria-labelledby = "heading<?php echo $row->ID; ?>">
				<div class = "panel-body panel-any">
					<div class = "table-responsive">
						<table class = "table table-condensed">
							<thead>
								<tr>
									<th>#</th>
									<th>ФИО</th>
									<th>Кол-во правильных</th>
									<th>Оценка</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$r = 0; 
									foreach ($results['results']->result() as $result) {
										if ($result->ID_TEST == $row->ID) {
											$r++;
								?>
									<tr>
										<th scope = "row"><?php echo $r;?></th>	
										<td><a href = "<?php echo base_url() . 'index.php/admin/view_result/' . $result->ID; ?>"><?php echo $result->NAME; ?></a></td>
										<td><?php echo $result->POINTS;?></td>
										<td><?php $percent = ($result->POINTS / $row->NUM_QUESTIONS) * 100; if ($percent < 50) echo "2"; else if ($percent < 75) echo "3"; else if ($percent < 90) echo "4"; else echo "5";?></td>
									</tr>
								<?php }} ?>			
							</tbody>
						</table>
					</div>
					
				</div>
			</div>

		</div>
	<?php
		}
	?>

