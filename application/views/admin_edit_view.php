<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = 'container'>
	<div class = "col-md-12 col-xs-12">
		<form enctype = "multipart/form-data" action = "<?php echo base_url()?>index.php/admin/edit_question" method = "POST" role = "form" onSubmit = "tinymce.triggerSave();">
			<div class = "alert alert-danger hidden" id = "error"> <p id = "error-message"></p> </div>
			<div class = "panel panel-default">
				<div class = "panel-heading">					
					<h4 class = "panel-title">
						<h4>
							Редактирование теста
						</h4>								
					</h4>
				</div>
				<div class = "panel-body" >
					<select class = "form-control" name = 'select_test' id = 'select_test'>
						<option value = '0'>Выберите тест...</option>
						<?php 
							foreach ($tests->result() as $row) {
								echo "<option value = '" . $row->ID . "'>" . $row->SECTION . "</option>";
							}
						?>
					</select>
					<div class = "skip" style = "padding-top: 10px;"></div>
					<select class = 'form-control' name = 'select_question' id = 'select_question' disabled>
						<option value = '0'>Сначала выберите тест...</option>
					</select>

					<div class = "skip" style = "padding-top: 20px;"></div>
					<hr>

					<div class = "form-group" style = "margin-top: 20px;">
						<label for = "question">Отредактируйте вопрос</label>
						<input type = 'text' class = 'form-control' id = "question" name = 'question' tabindex = 1 autocomplete = "off" />
					</div>
					<hr>
					<div class = "form-group" style = "padding-top: 10px;">
						<label for = "ans_0">Отредактируйте ответы. Номер правильного изменить нельзя. </label>						
						<?php for ($i = 0 ; $i < 5 ; $i++) {?>
							<div class = "radio row" style = "padding-left: 15px;">
								<label class = "col-md-12">
									<input type = "radio" style = "margin-top: 10px;" disabled name = "new_question" id = "new_question" value = "<?php echo $i + 1;?>"> 
									<div>
										<input type = "text" class = "form-control" disabled placeholder = "Ответ №<?php echo $i + 1;?>" id = "ans_<?php echo $i;?>" name = "ans_<?php echo $i + 1; ?>" tabindex = <?php echo $i + 2;?> autocomplete = "off">	
									</div>									
								</label>
							</div>
							<div></div>
						<?php } ?>
					</div>

				</div>
			</div>
			<div class = "btn-toolbar" role = "toolbar">
				<div class = "btn-group pull-left" role = "group">
					<button type = 'submit' name = 'add' id = 'add' class = "btn btn-primary" disabled = "disabled">Добавить новый вопрос</button>			
					<button type = 'button' name = 'update' id = 'update' class = 'btn btn-success' disabled = "disabled" >Обновить выбранный вопрос</button>
					<button type = 'button' name = 'delete' id = 'delete' class = "btn btn-danger" disabled = "disabled">Удалить выбранный вопрос </button>
				</div>
				<div class = "btn-group pull-right" role = "group">					
					<button type = 'button' name = 'delete_all' id = 'delete_all' class = "btn btn-danger" disabled = "disabled">Закрыть тест</button>
				</div>
			</div>			
		</form>
	</div>	
</div>

<script src="<?php echo base_url()."assets/js"?>/jquery.jrumble.1.3.min.js" type = 'text/javascript'></script>	
<script src = "<?php echo base_url()."assets/js"?>/tinymce/js/tinymce/tinymce.min.js" type = 'text/javascript'></script>
<script src="<?php echo base_url()."assets/js"?>/edit_util.js" type = 'text/javascript'></script>	
<script src="<?php echo base_url()."assets/js"?>/jquery.formstyler.min.js" type = 'text/javascript'></script>
