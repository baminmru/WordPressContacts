<?php 
require_once('auth.php');
?><!DOCTYPE html>
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
    			<h3>Заблокировнные IP</h3>
    		</div>
			<div class="row">
				 <p>
					<a href="ip_create.php" class="btn btn-success">Создать</a>
				</p>
				
				
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>IP</th>
						  <th>Сайт</th>
						  <th></th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					   include 'database.php';
					   $pdo = Database::connect();
					   $sql = 'SELECT ip,clientkey.domain,lockedip.clientkey FROM lockedip join clientkey on lockedip.clientkey=clientkey.clientkeyid ORDER BY ip DESC';
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
							   	echo '<td>'. $row['ip'] . '</td>';
								echo '<td>'. $row['domain'] . '</td>';
							   	echo '<td>'.'<a class="btn btn-danger" href="ip_delete.php?id='.$row['ip'].'&key='.$row['clientkey'].'">Удалить</a>';
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