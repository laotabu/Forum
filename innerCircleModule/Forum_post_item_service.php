<?php
	$pach= $_SERVER['DOCUMENT_ROOT'];
	date_default_timezone_set("PRC");//设置时区
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	//获取当前用户
	session_start();
	if(isset($_SESSION['u_id'])){
		$comment_user_id=$_SESSION['u_id'];
		$comment_user_name=$_SESSION['u_name'];
	}else{
		header("Location:../login.html");
	}
	
	if(!empty($_POST)){
			//$comment_user_id=$_POST['comment_user_id'];
			$content=$_POST['content'];
			$father_id=$_POST['father_id'];
			$reply_u_id=$_POST['reply_u_id'];
			$post_id=$_POST['post_id'];
			$grade=$_POST['grade'];
			$comment_time = date("Y-m-d h:i:s");
			$sql="insert into comment(comment_user_id,comment_user_name,content,father_id,grade,post_id,reply_u_id,comment_time)value('$comment_user_id','$comment_user_name','$content','$father_id','$grade','$post_id','$reply_u_id','$comment_time')";

			$result=$mysql->exec($sql);
			if($result==1){
				//一级评论额外添加数据
				if($grade==1){
					//表post post_sum(一级评论的数量)数据+1
					$data=$mysql->exec("select * from post where Id=$post_id");
					$post_sum=$data['post_sum'];
					$post_sum+=1;
					$addPostSumResult=$mysql->exec("update post set post_sum=$post_sum where Id=$post_id");
					if($addPostSumResult==1){
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
			$praised_post_id=isset($_GET['praised_post_id'])?$_GET['praised_post_id']:'';
			$praised_user_id=isset($_GET['praised_user_id'])?$_GET['praised_user_id']:'';
			$sql="select * from praise where praised_user_id=$praised_user_id and praised_post_id=$praised_post_id";
			$result=$mysql->exec($sql);
			if($result){
				echo "0";
			}else{
				//表praise插入数据
				$insertResult=$mysql->exec("insert into praise(praised_user_id,praised_post_id)value('$praised_user_id','$praised_post_id')");
			
				//表post post_hot数据+1
				$data=$mysql->exec("select * from post where Id=$praised_post_id");
				$post_hot=$data['post_hot'];
				$post_hot+=1;
				$changePostHotResult=$mysql->exec("update post set post_hot=$post_hot where Id=$praised_post_id");


				//表user u_hot+1
				$data=$mysql->exec("select * from user where u_id=$praised_user_id");
				$u_hot=$data['u_hot'];
				$u_hot+=1;
				$changeUserHotResult=$mysql->exec("update user set u_hot=$u_hot where u_id=$praised_user_id");

				if($insertResult==1 && $changePostHotResult==1 && $changeUserHotResult==1){
					echo "1";
				}else{
					echo "2";
				}
			}
		}
	}
?>