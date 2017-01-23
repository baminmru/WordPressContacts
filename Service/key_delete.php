<?php 
require_once('auth.php');

	require 'database.php';
	$id = 0;
	
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( !empty($_POST)) {
		// keep track post values
		$id = $_POST['id'];
		
		// delete data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM clientkey  WHERE clientkeyid = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		Database::disconnect();
		header("Location: key.php");
		
	} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
    
    			<div class="span10 offset1">
    				<div class="row">
		    			<h3>Удалить ключ</h3>
		    		</div>
		    		
	    			<form class="form-horizontal" action="key_delete.php" method="post">
	    			  <input type="hidden" name="id" value="<?php echo $id;?>"/>
					  <p class="alert alert-error">Уверены что хотите удалить ключ доступа ?</p>
					  <div class="form-actions">
						  <button type="submit" class="btn btn-danger">Да</button>
						  <a class="btn" href="key.php">Нет</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>