<?php 
require_once('auth.php');
?>
<?php 
	
	require 'database.php';

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( null==$id ) {
		header("Location: key.php");
	}
	
	if ( !empty($_POST)) {
		// keep track validation errors
		$nameError = null;
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
		
	
		if (empty($keypaused)) {
			$keypaused=0;
		
		}
		
		// update data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE clientkey  set domain = ?, keypaused =? WHERE clientkeyid = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($domain,$keypaused,$id));
			Database::disconnect();
			header("Location: key.php");
		}
	} else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT domain,keypaused FROM clientkey where clientkeyid = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		$domain = $data['domain'];
		$keypaused = $data['keypaused'];
		Database::disconnect();
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
		    			<h3>Изменить данные ключа</h3>
		    		</div>
    		
	    			<form class="form-horizontal" action="key_update.php?id=<?php echo $id?>" method="post">
					  <div class="control-group <?php echo !empty($domainError)?'error':'';?>">
					    <label class="control-label">Домен</label>
					    <div class="controls">
					      	<input name="domain" type="text"  placeholder="Домен" value="<?php echo !empty($domain)?$domain:'';?>">
					      	<?php if (!empty($domainError)): ?>
					      		<span class="help-inline"><?php echo $domainError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					 
					  <div class="control-group <?php echo !empty($keypausedError)?'error':'';?>">
					    <label class="control-label">Блокирован</label>
					    <div class="controls">
					      	<input name="keypaused" type="checkbox"   value="1" <?php echo ($keypaused=='1')?'checked':'';?> >
					      	
					    </div>
					  </div>
					  <div class="form-actions">
						  <button type="submit" class="btn btn-success">Изменить</button>
						  <a class="btn" href="key.php">Назад</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>