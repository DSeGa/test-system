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
	<script src="<?php echo base_url()."assets/js"?>/util.js" type = 'text/javascript'></script>
	<link rel = "shortcut icon" href = "<?php echo base_url()?>assets/images/favicon.ico" type = "image/x-icon">
	<link rel = "icon" href = "<?php echo base_url()?>assets/images/favicon.ico" type = "image/x-icon">
</head>



<nav class = "navbar navbar-default navbar-fixed-top">
	<div class = "container">
		<div class = "navbar-header">
			<button type = "button" class = "navbar-toggle collapsed" data-toggle = "collapse" data-target = "#main-header-menu" aria-expanded = "false">
				<span class = "sr-only">Toggle navigation</span>
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
			</button>
			<a class = "navbar-brand" href = "#" style = "margin-top: -5px">
				<img src = "<?php echo base_url()?>assets/images/brand.png" width = 30 height = 30>
			</a>
		</div>

		<div class = "collapse navbar-collapse" id = "main-header-menu">
			<div class = "top-menu-links">
				<ul class = "nav navbar-nav top-links">
					<?php echo $top_menu; ?>
				</ul>	
			</div>
			
			<form action = "<?php echo base_url()?>index.php/main/do_logout" method = "POST" class = "navbar-form navbar-right" role = "logout">				
				<button type = 'submit' class = 'btn btn-default'> Выход </button>
			</form>			
			<p class = "navbar-text navbar-right"><?php echo $name;?></p>
		</div>
	</div>
</nav>