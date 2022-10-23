<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/x-mathjax-config">
	MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});
</script>
<script type="text/javascript" async
 	src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS_CHTML">
</script>



<div class = "container">
	<div style = "width: 100%; float: left; ">
		<form action = "<?php echo base_url()?>index.php/main/check_test" role = "form" method = "POST" onSubmit="return checkForm();">	
			<!-- Вот этот элемент будет трястись :) -->
			<div class = "alert alert-danger hidden" id = "error">
				<p id = "error-message"></p>
			</div>
			<?php $question = 0; ?>
			<?php foreach ($test["questions"]->result() as $row) { ?>		
				<div class = "panel panel-default" id = "question_flag_<?php echo $question + 1; ?>" value = "false">
					<div class = "panel-heading">
						<h4 class = "panel-title" style = "text-align: justify;">
							<?php $question++; echo "<b>Вопрос №" . $question . ".</b> " . $row->TEXT;?>
						</h4>
					</div>
					<div class = "panel-body panel-any">
						<?php $cnt = 0; ?>
						<?php foreach ($test["answers"]->result() as $row_a) { ?>
							<?php if ($row_a->ID_QUESTION == $row->ID) { ?>
								<div class = "radio question-tab">
									<label >
										<input type = "radio" onclick = "update_answered(<?php echo $question; ?>);" name = "question_<?php echo $row->ID;?>" value = "<?php echo $row_a->ID;?>">
										<?php echo $row_a->TEXT; ?>
									</label>
								</div>
								<?php $cnt++; 
									if ($cnt < 5) echo "<hr>";
								?>
							<?php } ?>
						<?php } ?>	
					</div>			
				</div>
			<?php } ?>
			<input type = "hidden" name = "test_id" value = "<?php echo $test_id;?>">
			<button type = "submit" id = "finish" class = "btn btn-primary pull-right">
				Проверить!
			</button>
		</form>
		<div id = "messages">
		</div>
	</div>

	<div id = "q_answered" class = "pull-right" style = "padding-left: 2px">
		<div class = "panel panel-primary" style = "width: 100px;">
			<div class = "panel-heading">
				<h4 class = "panel-title">
					Отвечено
				</h4>
			</div>
			<div class = "panel-body" style = "text-align: center;">
				<span class = "badge" id = "q_answered_cnt">0/25</span>								
			</div>			
			<button class = "btn btn-primary btn-xs" style = "width: 100%" id = "search">Найти<br>без ответа</button>
		</div>
	</div>
</div>

<script src="<?php echo base_url()."assets/js"?>/jquery.hc-sticky.min.js" type = 'text/javascript'></script>
<script src="<?php echo base_url()."assets/js"?>/jquery.jrumble.1.3.min.js" type = 'text/javascript'></script>	
<script src="<?php echo base_url()."assets/js"?>/test_util.js" type = 'text/javascript'></script>	
