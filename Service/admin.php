<?php 
require_once('auth.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
<h1>СМС API</h1>
    <div class="container">
    		<div class="row">
    			<h3>Управление</h3>
    		</div>
			<div class="row">
				<p>
					<a href="key.php" class="btn btn-success">Ключи доступа</a>
				</p>
				<p>
					<a href="ip.php" class="btn btn-success">Заблокировнные IP</a>
				</p>
				<p>
					<a href="phone.php" class="btn btn-success">Заблокировнные телефоны</a>
				</p>
				<p>
					<a href="sms.php" class="btn btn-success">Отправленные СМС</a>
				</p>
				<p>
					<a href="logout.php" class="btn btn-danger">Выход</a>
				</p>
				
    	</div>
    </div> <!-- /container -->
  </body>
</html>