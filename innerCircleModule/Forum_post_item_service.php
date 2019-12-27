<?php
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	//获取当前用户
	session_start();
	if(isset($_SESSION['u_id'])){
		$u_id=$_SESSION['u_id'];
		$u_name=$_SESSION['u_name'];
	}else{
		header("Location:../login.html");
	}
	
	if(!empty($_POST)){
			$to_user_id=$_POST['u_id'];
			
			$comment=$_POST['comment'];
			$father_id=$_POST['father_id'];
			$reply_user_id=$_POST['reply_user_id'];
			$post_id=$_POST['post_id'];
			$grade=$_POST['grade'];
			$sql="insert into `comment`(u_id,u_name,comment,father_id,grade,post_id,reply_user_id)value('$u_id','$u_name','$comment','$father_id','$grade','$post_id','$reply_user_id')";

			$result=$mysql->exec($sql);
			if($result==1){
				//一级评论额外添加数据
				if($grade==1){
					//表post post_sum数据+1
					$sql1="select * from post where Id=$post_id";
					$data=$mysql->exec($sql1);
					$post_sum=$data['post_sum'];
					$post_sum+=1;
					$sql1="update post set post_sum=$post_sum where Id=$post_id";
					$result1=$mysql->exec($sql1);
					//表user user_sum+1
					$sql2="select * from user where u_id=$u_id";
					$data=$mysql->exec($sql2);
					$u_post_sum=$data['u_post_sum'];
					$u_post_sum+=1;
					$sql2="update user set u_post_sum=$u_post_sum where u_id=$to_user_id";
					$result2=$mysql->exec($sql2);
					if($result1==1 && $result2==1){
						$referer = $_SERVER['HTTP_REFERER']; //来路信息。就是上一页
						header("Location: $referer"); //浏览器跳转
					}
				}else{
					$referer = $_SERVER['HTTP_REFERER']; //来路信息。就是上一页
					header("Location: $referer"); //浏览器跳转
				}
			}else{
				echo "出错了！！";
			}
	}
	if(!empty($_GET)){
		$op=isset($_GET['op'])?$_GET['op']:'';
		if($op==1){	
			$prased_post_id=isset($_GET['post_id'])?$_GET['post_id']:'';
			$to_user_id=isset($_GET['u_id'])?$_GET['u_id']:'';
			$sql="select * from praise where u_id=$to_user_id and prased_post_id=$prased_post_id";
			$result=$mysql->exec($sql);
			if($result){
				echo "0";
			}else{
				//表praise插入数据
				echo "string";
				$sql="insert into praise(u_id,prased_post_id)value('$to_user_id','$prased_post_id')";
				$result=$mysql->exec($sql);
				//表post post_hot数据+1
				$sql1="select * from post where id=$prased_post_id";
				$data=$mysql->exec($sql1);
				$post_hot=$data['post_hot'];
				$post_hot+=1;
				$sql1="update post set post_hot=$post_hot where Id=$prased_post_id";
				$result1=$mysql->exec($sql1);
				//表user u_hot+1
				$sql2="select * from user where u_id=$to_user_id";
				$data=$mysql->exec($sql2);
				$u_hot=$data['u_hot'];
				$u_hot+=1;
				$sql2="update user set u_hot=$u_hot where u_id=$to_user_id";
				$result2=$mysql->exec($sql2);
				
				if($result==1 && $result1==1 && $result2==1){
					echo "1";
				}else{
					echo "2";
				}
			}
		}
	}
?>