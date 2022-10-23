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
	<div class = "panel panel-primary">
		<div class = "panel-heading">
			<h4 class = "panel-title">
				Результат
			</h4>
		</div>
		<div class = "panel-body">
			<?php if ($lastname != "") { ?>
			<p>
				<b>Ученик:</b> <?php echo $lastname; ?>
			</p>
			<?php } ?>
			<p>
				<b>Правильных ответов:</b> <?php echo $history['total']; ?>
			</p>
			<p>
				<b>Полученная оценка:</b> <?php $percent = ($history['total'] / $history['num_q']) * 100; if ($percent < 50) echo "2"; else if ($percent < 75) echo "3"; else if ($percent < 90) echo "4"; else echo "5";?>
			</p>
			<p>
				<b>Дата завершения:</b> <?php echo substr($history['finish_date'] , 0 , strlen($history['finish_date']) - 7); ?>
			</p>
		</div>
	</div>
	<?php $question = 0; ?>
	<?php foreach ($history["questions"]->result() as $row) { ?>		
		<div class = "panel panel-default">
			<div class = "panel-heading">
				<h4 class = "panel-title">
					<?php $question++; echo "<b>Вопрос №" . $question . ".</b> " . $row->TEXT;?>
				</h4>
			</div>
			<div class = "panel-body panel-any">
				<?php $cnt = 0; ?>
				<?php foreach ($history["answers"]->result() as $row_a) { ?>
					<?php if ($row_a->ID_QUESTION == $row->ID) { ?>
						<?php $is_student_answer = false; $is_real_answer = false; ?>
						<?php foreach ($history['student_answers']->result() as $row_ans) {
							if ($row_ans->ID_ANSWER == $row_a->ID) $is_student_answer = true;
						} ?>
						<?php if ($row_a->ID == $row->ID_ANSWER) $is_real_answer = true;
						?>
						<?php if ( !$is_student_answer && !$is_real_answer) echo "<div class = 'radio question-tab disabled'>"?>
						<?php if ( !$is_student_answer && $is_real_answer) echo "<div class = 'radio question-tab disabled bg-success'>"?>
						<?php if ( $is_student_answer && !$is_real_answer) echo "<div class = 'radio question-tab disabled bg-danger'>"?>
						<?php if ( $is_student_answer && $is_real_answer) echo "<div class = 'radio question-tab disabled bg-success'>"?>
							<label>
								<input type = "radio" disabled <?php if ($is_student_answer) echo "checked";?>>
								<?php echo $row_a->TEXT; ?>
							</label>
						</div>
						<?php $cnt++; if ($cnt < 5) echo "<hr>";?>
					<?php } ?>
				<?php } ?>	
			</div>			
		</div>
	<?php } ?>
	<a href = "<?php echo $back; ?>" class = "btn btn-primary pull-right" >Закончить обзор</a>
</div>