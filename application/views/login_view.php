<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<head>
	<meta charset = "utf-8">
	<title> Система тестирования </title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap.min.css'?>" media = 'screen'>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/bootstrap-theme.min.css'?>" media = 'screen'>	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/css/style.css'?>" media = 'screen'>	
	<script src="<?php echo base_url()."assets/js"?>/jquery-2.1.4.min.js" type = 'text/javascript'></script>
	<script src="<?php echo base_url()."assets/js"?>/bootstrap.min.js" type = 'text/javascript'></script>
	<link rel = "shortcut icon" href = "<?php echo base_url()?>assets/images/favicon.ico" type = "image/x-icon">
	<link rel = "icon" href = "<?php echo base_url()?>assets/images/favicon.ico" type = "image/x-icon">
</head>

<div class = 'container'>
	<div class = "parent">
		<div class = "col-md-3 col-xs-3">
			<form action = "<?php echo base_url()?>index.php/main/do_login" method = "POST" role = "form">
				<div class = 'form-group'>
					<h3 class = "">Вход в систему</h3>
				</div>

				<?php 
					if ($this->session->login_error) {
						$this->session->unset_userdata('login_error');
				?>
				<div class = "alert alert-danger alert-thin" role = "alert" >
					<span class = "glyphicon glyphicon-exclamation-sign" aria-hidden = "true"></span>
					<span class = "sr-only">Ошибка</span>
					Неправильные логин/пароль
				</div>		
				<?php
					}
				?>


				<div class = "form-group">
					<input type = 'text' class = 'form-control' name = 'login' placeholder = 'Имя пользователя'/>
				</div>
				<div class = "form-group">
					<input type = 'password' class = 'form-control' name = 'password' placeholder = 'Пароль'/>
				</div>
				<div class="form-group">
					<!-- <input type = "checkbox" name = "teacher" > Я учитель	 -->
					<input type = 'submit' name = 'submit' class = 'btn btn-primary pull-right'value = 'Вход' />	
				</div>
				
			</form>
		</div>	
	</div>
</div>
