<?php 
require_once('auth.php');

	
	require 'database.php';

	if ( !empty($_POST)) {
		// keep track validation errors
		$domainError = null;
		$keyvalueError = null;
		$keypausedError = null;
		
		// keep track post values
		$domain = $_POST['domain'];
		$keypaused = $_POST['keypaused'];
		
		// validate input
		$valid = true;
		if (empty($domain)) {
			$domainError = 'Please enter domain';
			$valid = false;
		}
		
		
		
		// insert data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO clientkey (clientkeyid,domain,keyvalue,keypaused,keycreattime) values(uuid(),?, concat(uuid(),uuid(),uuid()), ?,now())";
			$q = $pdo->prepare($sql);
			$q->execute(array($domain,$keypaused));

			Database::disconnect();
			header("Location: key.php");
		}
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
		    			<h3>Создать ключ</h3>
		    		</div>
    		
	    			<form class="form-horizontal" action="key_create.php" method="post">
					  <div class="control-group <?php echo !empty($domainError)?'error':'';?>">
					    <label class="control-label">Домен</label>
					    <div class="controls">
					      	<input name="domain" type="text"  placeholder="domain" value="<?php echo !empty($domain)?$domain:'';?>">
					      	<?php if (!empty($domainError)): ?>
					      		<span class="help-inline"><?php echo $domainError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					
					  <div class="control-group <?php echo !empty($keypausedError)?'error':'';?>">
					    <label class="control-label">Блокирован</label>
					    <div class="controls">
					      	<input name="keypaused" type="checkbox"  placeholder="0" value="<?php echo !empty($keypaused)?$keypaused:0;?>">
					      	
					    </div>
					  </div>
					  <div class="form-actions">
						  <button type="submit" class="btn btn-success">Создать</button>
						  <a class="btn" href="key.php">Назад</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>