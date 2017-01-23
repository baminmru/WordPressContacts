<?php
	//Start session
	session_start();
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
<title>Выход</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Успешное завершение </h1>
<p align="center">&nbsp;</p>
<h4 align="center" class="err">Вы вышли из системы СМС API.</h4>
<p align="center">Нажмите чтобы войти <a href="login-form.php" class="btn btn-success" >Войти снова</a></p>
</body>
</html>
