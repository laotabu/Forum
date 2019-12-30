<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	// include $pach.'/Forum/MYSQL.php';
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	$sql="select * from `notice`";
	$data=json_decode($mysql->queryAll($sql));
	session_start();
	$u_id=$_SESSION['u_id'];
?>
<html>
	<header>
		<meta charset="utf-8">
		<title>管理员界面-模块管理</title>
		<link rel="stylesheet" href="../assets/css/all.css">
		<link rel="stylesheet" href="../assets/css/module_style.css">
		<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
		<script type="text/javascript" src="../assets/js/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.js"></script>
	</header>
	<body>
		<!-- <?php //include './header.html' ?> -->
		<?php include 'manageWholePage.html' ?>
		<div id="content">
			<!-- 添加内容 -->
			<div class="add">
				<!-- 公告栏更新 -->
				<form class="form-horizontal" action="manage_module_core.php" method="get">
				  <div class="form-group">
					  <label for="inputPassword3" class="col-sm-1 control-label">原公告</label>
				  		<div class="col-sm-11">
							<textarea class="form-control" rows="2" disabled>
								<?php 
									if ($data!=null) {
										echo $data[0]->notice;
									}else {
										$mysql->exec("insert into notice(u_id,notice) values('$u_id','暂无公告')");
									}
							 		
							//print_r($data);
							?></textarea>
						</div>
				  </div>
				  <div class="form-group">
					  <label for="inputPassword3" class="col-sm-1 control-label">新公告</label>
				  		<div class="col-sm-11">
							<input type="hidden" name="op" value="5">
							<textarea class="form-control" rows="2" name="notice"></textarea>
						</div>
				  </div>				  
				  <div class="form-group">
					<div class="col-sm-offset-11 col-sm-1">
					  <button type="submit" class="btn btn-default">保存</button>
					</div>
				  </div>
				</form>	
			</div>
		</div>
		<script>
			$(function(){
				//导航选中特效
				$("#nav li:contains('公告')").addClass("li_on");
				$.ajax({
					url:"../index.php",
					type:"POST",
					data:{data:'public_user'},
					success: function(msg){
						if(msg==1){
							window.location.href="../login.html";
						}
					}
				})
			});
		</script>
	</body>
</html>