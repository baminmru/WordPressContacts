<?php 
require_once('auth.php');
 
	
	require 'database.php';

	if ( !empty($_POST)) {
		// keep track validation errors
		$domainError = null;
		
			// keep track validation errors
		$keyError = null;
	
		// keep track post values
		$phone = $_POST['phone'];
		
		$key = $_POST['key'];
		
		// validate input
		$valid = true;
		
		if (empty($phone)) {
			$domainError = 'Please enter phone';
			$valid = false;
		}
		
		if (empty($key)) {
			$keyError = 'Please enter domain';
			$valid = false;
		}
		
		// insert data
		if ($valid) {
			try{
				$pdo = Database::connect();
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = "INSERT INTO lockedphone (phone,clientkey) values(?,?)";
				$q = $pdo->prepare($sql);
				$q->execute(array($phone,$key));
				Database::disconnect();
				header("Location: phone.php");
			}catch(Exception $e){
				$domainError = $e->getMessage();
			}
			
			
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
		    			<h3>Заблокировать номер</h3>
		    		</div>
    		
	    			<form class="form-horizontal" action="phone_create.php" method="post">
					  <div class="control-group <?php echo !empty($domainError)?'error':'';?>">
					    <label class="control-label">Номер мобильного</label>
					    <div class="controls">
					      	<input name="phone" type="text"  placeholder="phone" value="<?php echo !empty($phone)?$phone:'';?>">
					      	<?php if (!empty($domainError)): ?>
					      		<span class="help-inline"><?php echo $domainError;?></span>
					      	<?php endif; ?>
					    </div>
						
						<label class="control-label">Домен</label>
					    <div class="controls">
							<select name="key" >							
							<?php    $pdo = Database::connect();
							   $sql = 'SELECT domain,clientkeyid FROM clientkey where keypaused is null or keypaused=0 ORDER BY domain';
							   foreach ($pdo->query($sql) as $row) {
										if(!empty($key)){
											echo '<option ';
											if($key==$row['clientkeyid'] ) echo ' selected ';
											echo ' value="'. $row['clientkeyid'] . '">'.$row['domain'] .'</option>';
										}else{
											echo '<option value="'. $row['clientkeyid'] . '">'.$row['domain'] .'</option>';
										}
							   }
							   Database::disconnect();
							?>
							</select>							
					      	<?php if (!empty($keyError)): ?>
					      		<span class="help-inline"><?php echo $keyError;?></span>
					      	<?php endif; ?>
							
							
					    </div>
						
					  </div>
					
					
					  <div class="form-actions">
						  <button type="submit" class="btn btn-success">Записать</button>
						  <a class="btn" href="phone.php">Назад</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>