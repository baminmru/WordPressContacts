<?php 
require_once('auth.php');
$page=0;
if ( !empty($_GET)) {
	$page = $_GET['page'];
}
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
    			<h3>Отправленные СМС</h3>
    		</div>
			<div class="row">
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
						 <th>Кому</th>
						 <th>Текст</th>
		                  <th>Создан</th>
						  <th>Статус</th>
						  <th>Сайт</th>
		                  <th>IP клиента</th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					   include 'database.php';
					   $pdo = Database::connect();
					   $sql = 'SELECT count(*) cnt  FROM sms join clientkey on sms.clientkeyid=clientkey.clientkeyid;';
					   $total=0;
					  
					  
					   foreach ($pdo->query($sql) as $row) {
					   $total=$row['cnt'] ;
						}
						 $maxpage=ceil($total/10);
						  echo '<h4>Страница '.($page+1).'  из '.$maxpage.'</h4>';
					   if($total <= 10 ){
							$sql = 'SELECT smsto, smstext,  sms.createtime, smsstatus , clientkey.domain,clientip FROM sms join clientkey on sms.clientkeyid=clientkey.clientkeyid order by sms.createtime desc;';	
					   }else{
						    $sql = 'SELECT smsto, smstext,  sms.createtime, smsstatus , clientkey.domain,clientip FROM sms join clientkey on sms.clientkeyid=clientkey.clientkeyid order by sms.createtime desc limit '.($page*10).',10;';
					   }
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
							   	echo '<td>'. $row['smsto'] . '</td>';
								echo '<td>'. $row['smstext'] . '</td>';
								echo '<td>'. $row['createtime'] . '</td>';
								echo '<td>'. $row['smsstatus'] . '</td>';
								echo '<td>'. $row['domain'] . '</td>';
								echo '<td>'. $row['clientip'] . '</td>';
							   	echo '</tr>';
					   }
					   Database::disconnect();
					  ?>
				      </tbody>
	            </table>
				
				<?php 
				echo '<a href="sms.php?page=0" class="btn btn-success">првая </a> ';
				if($page >0)	
					echo '  <a href="sms.php?page='.($page-1).'" class="btn btn-success">пред. </a> ';
				if($page+1 < $maxpage)
					echo '  <a href="sms.php?page='.($page+1).'" class="btn btn-success">след. </a> ';
				echo '  <a href="sms.php?page='.($maxpage-1).'" class="btn btn-success">последняя </a>';
				?>
				
				
    	</div>
    </div> <!-- /container -->
  </body>
</html>