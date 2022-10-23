<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = "container">
	<div class = "panel-group" id = "accordion" role = "tablist" aria-multiselectable = "true">
	<?php 
		foreach ($history['subjects']->result() as $row) {
	?>
		<div class = "panel panel-default">
			
			<div class = "panel-heading" role = "tab" id = "<?php echo 'heading' . $row->ID; ?>">
				<h4 class = "panel-title">
					<a role = "button" data-toggle = "collapse" data-parent = "#accordion" href = "#collapse<?php echo $row->ID;?>" aria-expanded = "true" aria-controls = "collapse<?php echo $row->ID;?>">
						<?php echo $row->SUBJECT_NAME; ?>
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
									<th>Раздел</th>
									<th>Результат</th>
									<th>Оценка</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$r = 0; 
									foreach ($history['tests']->result() as $test) {
										if ($test->ID_SUBJECT == $row->ID) {
											$r++;
								?>
									<tr>
										<th scope = "row"><?php echo $r;?></th>	
										<td><a href = "<?php echo base_url();?>index.php/main/view_result/<?php echo $test->ID;?>"><?php echo $test->SECTION; ?></a></td>
										<td><?php echo $test->POINTS . "/" . $test->NUM_QUESTIONS;?></td>
										<td><?php $percent = ($test->POINTS / $test->NUM_QUESTIONS) * 100; if ($percent < 50) echo "2"; else if ($percent < 75) echo "3"; else if ($percent < 90) echo "4"; else echo "5";?></td>
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

