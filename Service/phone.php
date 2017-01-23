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
<h1><a href="admin.php" >СМС API</a></h1>
    <div class="container">
    		<div class="row">
    			<h3>Заблокировнные телефонные номера</h3>
    		</div>
			<div class="row">
				
				<p>
					<a href="phone_create.php" class="btn btn-success">Создать</a>
				</p>
			
				
				
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>Телефон</th>
						  <th>Сайт</th>
						  <th></th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					   include 'database.php';
					   $pdo = Database::connect();
					  // $sql = 'SELECT phone FROM lockedphone ORDER BY phone DESC';
					   $sql = 'SELECT phone,clientkey.domain,lockedphone.clientkey FROM lockedphone join clientkey on lockedphone.clientkey=clientkey.clientkeyid ORDER BY phone DESC';
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
							   	echo '<td>'. $row['phone'] . '</td>';
								echo '<td>'. $row['domain'] . '</td>';
							   	echo '<td>'.'<a class="btn btn-danger" href="phone_delete.php?id='.$row['phone'].'&key='.$row['clientkey'].'">Удалить</a>';
							   	echo '</td>';
							   	echo '</tr>';
					   }
					   Database::disconnect();
					  ?>
				      </tbody>
	            </table>
    	</div>
    </div> <!-- /container -->
  </body>
</html>