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
    			<h3>Ключи доступа</h3>
    		</div>
			<div class="row">
				<p>
					<a href="key_create.php" class="btn btn-success">Создать</a>
				</p>
				
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>Домен</th>
						  <th>Ключ</th>
		                  <th>Создан</th>
		                  <th>Блокирован</th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					   include 'database.php';
					   $pdo = Database::connect();
					   $sql = 'SELECT clientkey.*, md5(concat(clientkeyid,domain,keyvalue)) apikey FROM clientkey ORDER BY domain DESC';
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
							   	echo '<td>'. $row['domain'] . '</td>';
							   //	echo '<td>'. $row['keyvalue'] . '</td>';
								echo '<td>'. $row['apikey'] . '</td>';
							   	echo '<td>'. $row['keycreattime'] . '</td>';
								echo '<td>';  if($row['keypaused'] =='1') echo 'Да' ; else echo 'Нет';
								echo '</td>';
							   	echo '<td width=250>';
							 //  	echo '<a class="btn" href="read.php?id='.$row['clientkeyid'].'">Просмотр</a>';
							 //  	echo '&nbsp;';
							   	echo '<a class="btn btn-success" href="key_update.php?id='.$row['clientkeyid'].'">Изменить</a>';
							   	echo '&nbsp;';
							   	echo '<a class="btn btn-danger" href="key_regen.php?id='.$row['clientkeyid'].'">Перегенерировать</a>';
								echo '&nbsp;';
							   	echo '<a class="btn btn-danger" href="key_delete.php?id='.$row['clientkeyid'].'">Удалить</a>';
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