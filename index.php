<?php
	header("content-type:text/html;charset=utf-8"); 
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/MYSQL.php';
	$mysql=new Mysql();
	if(!empty($_POST)){
		$data=$_POST['data'];
		if($data=='allModule'){
			$sql="select * from `module`";
			$result=$mysql->queryAll($sql);
			echo json_encode($result);
		}else if($data=='allPost'){
			$sql="select * from `post` where `audit_result`=1 order by id desc limit 6";/*  order by id desc 按照id倒序输出 */
			$result=$mysql->queryAll($sql);
			echo json_encode($result);
		}else if($data=='hotPost'){
			$sql="select * from `post` where `audit_result`=1 order by post_hot desc limit 6";/* 点赞数越高排名越前 所以要倒序输出 */
			$result=$mysql->queryAll($sql);
			echo json_encode($result);
		}else if($data=='user'){ //普通用户信息
			session_start();
			if(isset($_SESSION['username'])&&$_SESSION['user_type']==2){
				$username=$_SESSION['username'];
				$sql="select * from `user` where `username`='$username'";
				$data=$mysql->queryOne($sql);
				echo json_encode($data);
			}else{
				echo 1;
			}
		}else if($data=='public_user'){ //普通用户信息
			session_start();
			if(isset($_SESSION['username'])&&$_SESSION['user_type']==1){
				$username=$_SESSION['username'];
				$sql="select * from `user` where `username`='$username'";
				$data=$mysql->queryOne($sql);
				echo json_encode($data);
			}else{
				echo 1;
			}
		}else if($data=='notice'){ //公告
			$sql="select * from `notice`";
			$data=$mysql->queryOne($sql);
			echo json_encode($data);
		}else if($data=='banner'){ //banner
			$sql="select * from `banner`";
			$data=$mysql->queryAll($sql);
			echo json_encode($data);
		}
	}
	if(!empty($_GET)){
		if(isset($_GET['back'])){
			session_start();
			if(isset($_SESSION['username'])){
				unset($_SESSION['username']);
			}
			if(isset($_SESSION['student_number'])){
				unset($_SESSION['student_number']);
			}
			if(isset($_SESSION['id'])){
				unset($_SESSION['id']);
			}
			if(isset($_SESSION['user_type'])){
				unset($_SESSION['user_type']);
			}
			session_destroy();//删除所有session
			header("location:login.html");
		}
	}
	
	
?>