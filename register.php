<?php 
header("content-type:text/html;charset=utf-8"); 
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	if (!empty($_POST)){
		$u_name=$_POST['u_name'];
		$u_passwd=$_POST['u_passwd'];
		$u_id=$_POST['u_id'];
		$u_email=$_POST['u_email'];
		$u_phone=$_POST['u_phone'];
		$type=$_POST['type'];
		
		/* 检测 用户名，邮箱，手机号 是否存在*/
		$mysql=new Mysql();
		$sql="select count(*) as sum from `user` where u_id='$u_id'";
		$result=$mysql->queryOne($sql);
		if($result['sum']!=0){
			echo '该学号已经注册，请直接登陆！';
			exit;
		}else{
			$sql="select count(*) as sum from `user` where u_name='$u_name'";
			$result=$mysql->queryOne($sql);
			if($result['sum']!=0){
				echo '用户名已被注册，请重新输入！';
				exit;
			}else{
				$sql="select count(*) as sum from `user` where u_email='$u_email'";
				$result=$mysql->queryOne($sql);
				if($result['sum']!=0){
					echo 'u_email已被注册，请重新输入！';
					exit;
				}else{
					$sql="select count(*) as sum from `user` where u_phone='$u_phone'";
					$result=$mysql->queryOne($sql);
					if($result['sum']!=0){
						echo '手机号已被注册，请重新输入！';
						exit;
					}else{
						$sql="insert into `user`(`u_id`,`u_name`,`u_passwd`,`u_email`,`u_phone`,`user_type`)value ('$u_id','$u_name','$u_passwd','$u_email','$u_phone','$type')";
						$result=$mysql->exec($sql);
						if($result=1){
							echo 1;
						}else{
							echo '注册失败！';
						}
			
					}
				}
			}
		}
		
	}	
?>