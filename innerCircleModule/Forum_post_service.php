<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'../Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	if(!empty($_GET)){
		$module_type=$_GET['module_type'];
		$sql="select * from module where module_type=$module_type";
		$data=$mysql->queryAll($sql);
		echo $data;
	}
	if(!empty($_POST)){
		if(isset($_FILES['post_cover_image'])&&$_FILES['post_cover_image']['error']==0){
			$post_title=$_POST['post_title'];
			$post_comment=$_POST['post_comment'];
			$post_cover_image=$_FILES['post_cover_image'];
			//判断上传的文件格式
			if(!($post_cover_image['type']=="image/png"||$post_cover_image['type']=="image/jpg")){
				echo "4";
				exit;
			};
			$path='../assets/cover_images/'.time().'.png';
			if(move_uploaded_file($post_cover_image['tmp_name'], $path)){
				//获取当前用户
				session_start();
				$post_user_id=$_SESSION['u_id'];
				$post_user_name=$_SESSION['u_name'];
				
				$post_module_type=$_POST['module_type'];
				$post_module_id=$_POST['module_id'];
				/* 查询 post_module_name */
				$sql="select * from module where module_type=$post_module_type and module_id='$post_module_id'";
				$data=$mysql->exec($sql);
				$post_module_name=$data['module_name'];
				$module_post_sum=$data['post_sum']+1;
				//插入发帖时间
				date_default_timezone_set("PRC");//设置时区
				$post_time = date("Y-m-d h:i:s");


				$sql="insert into post(post_title,post_comment,post_user_name,post_user_id,post_module_type,post_module_id,post_module_name,post_cover_image,post_time,audit)values('$post_title','$post_comment','$post_user_name','$post_user_id','$post_module_type','$post_module_id','$post_module_name','$path','$post_time',0)";
				$result=$mysql->exec($sql);
				if($result==1){
					//module 发帖数+1
					$sql="update module set post_sum='$module_post_sum' where  module_type='$post_module_type' and module_id=$post_module_id";
					$result1=$mysql->exec($sql);
					//获取用户信息 并修改
					$sql="select * from user where u_id=$post_user_id";
					$user=$mysql->exec($sql);
					//user发帖数+1
					$post_sum=$user['u_post_sum']+1;
					$sql="update user set u_post_sum=$post_sum where u_id=$post_user_id";
					$result2=$mysql->exec($sql);
					if($result1==1 && $result2==1){
						echo 1;
					}else{
						echo 0;
					} 
				}else{
					echo 3;
				}
			}
			
		}else{
			echo 2;
		}
	}
?>
