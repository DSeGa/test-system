<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class = 'container'>
	
	<div class = "col-md-12 col-xs-12">
		<form enctype = "multipart/form-data" action = "<?php echo base_url()?>index.php/admin/save_new_question" method = "POST" role = "form" onSubmit="tinymce.triggerSave(); return formFilled();">
			<div class = "alert alert-danger hidden" id = "error">
				<p id = "error-message"></p>
			</div>
			<div class = "panel panel-default">
				<div class = "panel-heading">					
					<h4 class = "panel-title">
						<h4>
							<?php echo $section; ?>. Вопрос №<?php echo $total_questions + 1;?>		
						</h4>								
					</h4>
				</div>
				<div class = "panel-body" >
					<div class = "form-group">
						<label for = "question">Введите вопрос</label>
						<input type = 'text' class = 'form-control' id = "question" name = 'question' tabindex = 1 autocomplete = "off"/>
						<input type = 'file' name = 'question_file' id = 'question_file'/>
					</div>
					<hr>
					<div class = "form-group" style = "padding-top: 10px;">
						<label for = "ans_0">Введите варианты ответов и выберите правильный</label>						
						<?php for ($i = 0 ; $i < 5 ; $i++) {?>
							<div class = "radio row" style = "padding-left: 15px;">
								<label class = "col-md-12">
									<input type = "radio" style = "margin-top: 10px;" name = "new_question" id = "new_question" value = "<?php echo $i + 1;?>"> 
									<div>
										<input type = "text" class = "form-control" placeholder = "Ответ №<?php echo $i + 1;?>" id = "ans_<?php echo $i;?>" name = "ans_<?php echo $i + 1; ?>" tabindex = <?php echo $i + 2;?> autocomplete = "off">	
										<input type = 'file' name = 'answer_file_<?php echo $i + 1; ?>' id = 'answer_file_<?php echo $i + 1; ?>' />
									</div>									
								</label>
							</div>
							<div></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<input type="submit" class = "btn btn-danger" name = "finish" value = "Последний вопрос!">
			<input type="submit" class = "btn btn-primary pull-right" name = "more" value = "Добавить еще вопрос">
		</form>
	</div>	
</div>

<script src="<?php echo base_url()."assets/js"?>/jquery.formstyler.min.js" type = 'text/javascript'></script>
<script src = "<?php echo base_url()."assets/js"?>/tinymce/js/tinymce/tinymce.min.js" type = 'text/javascript'></script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#question_file").styler({
			fileBrowse: 'Выбрать',
			filePlaceholder: 'Если к вопросу идет картинка, выберите её здесь...'
		});
		for (var i = 1 ; i <= 5 ; i++) {
			$("#answer_file_" + i).styler({
				fileBrowse: 'Выбрать',
				filePlaceholder: 'Если к ответу №' + i + ' идет картинка, выберите её здесь...'
			});
		}
		tinymce.init({
			selector: '#question',
			plugins: 'paste',
			menu: {},
			toolbar: 'undo redo | bold italic underline',
			force_br_newlines: true,
			paste_as_text: true
		});
	});
</script>
