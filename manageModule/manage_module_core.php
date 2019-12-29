<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';

	$mysql=new Mysql();
	//删除
	if(!empty($_GET)){
		$op=$_GET['op'];
		if($op==1){
			$module_id=$_GET['module_id'];
			$sql="delete from module where module_id='$module_id'";
			$result=$mysql->exec($sql);
			if($result==1){
				header('location:manage_module.php');
			}else{
				exit('数据库出错！');
			}
		}else if($op==2){ //帖子禁止
			$id=$_GET['id'];
			$reason=$_GET['reason'];
			$sql="update post set audit_result=10,audit=1,reason='$reason' where Id ='$id'";
			$result=$mysql->exec($sql);
			if($result==1){
				$referer = $_SERVER['HTTP_REFERER']; //来路信息。就是上一页
				header('location:manage_oldPost.php'); //浏览器跳转
			}else{
				exit('数据库出错！');
			}
		}else if($op==20){ //帖子 审核不通过
			$id=$_GET['id'];
			$reason=$_GET['reason'];
			$sql="update post set audit_result=0,audit=1,reason='$reason' where Id ='$id'";
			$result=$mysql->exec($sql);
			if($result==1){
				$referer = $_SERVER['HTTP_REFERER']; //来路信息。就是上一页
				header("location:manage_newPost.php"); //浏览器跳转
			}else{
				exit('数据库出错！');
			}
		}else if($op==3){ //删除用户
			$id=$_GET['u_id'];
			$sql="delete from user where u_id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				header('location:manage_user.php');
			}else{
				exit('数据库出错！');
			}
		}else if($op==4){
			$id=$_GET['u_id'];
			$is_enable=$_GET['is_enable'];
			//解除冻结/冻结
			if($is_enable==0 || $is_enable==2){
				//恢复正常状态并密码错误次数初始化0
				$sql="update user set is_enable=1,u_passwd_error=0 where u_id=$id";
				$result=$mysql->exec($sql);
				if($result==1){
					header('location:manage_user.php');
				}else{
					exit('数据库出错！');
				}
			}else{
				$sql="update user set is_enable=0, is_enable=2 where u_id=$id";
				$result=$mysql->exec($sql);
				if($result==1){
					header('location:manage_user.php');
				}else{
					exit('数据库出错！');
				}
			}
		}else if($op==5){
			$notice=$_GET['notice'];
			$sql="update notice set notice='$notice'";
			$result=$mysql->exec($sql);
			if($result==1){
				header('location:manage_notice.php');
			}else{
				exit('公告最多发布30字数！请重新输入！');
			}
		}else if($op==6){
			$id=$_GET['id'];
			$sql="update post set audit_result=1,audit=1 where Id ='$id'";
			$result=$mysql->exec($sql);
			if($result==1){
				header('location:manage_newPost.php');
			}else{
				exit('数据库出错！');
			}
		}
	}
	if(!empty($_POST)){
		$op=$_POST['op'];
		if($op==1){//数据更新
			$id=$_POST['id'];
			$module_explain=$_POST['module_explain'];
			$sql="update module set module_explain='$module_explain' where module_id='$id'";
			$result=$mysql->exec($sql);
			if($result==1){
				header('location:manage_module.php');
			}else{
				exit('数据库出错！8');
			}
		}else if($op==2){//数据插入
			$module_type=$_POST['module_type'];
			$module_name=isset($_POST['module_name'])?$_POST['module_name']:'';
			$module_explain=$_POST['module_explain'];
			//查询模块名是否重复
			$sql="select * from module where module_name='$module_name'";
			$result=$mysql->exec($sql);
			if(!$result){
				$sql="insert into module(module_type,module_name,module_explain)value('$module_type','$module_name','$module_explain')";
				$result=$mysql->exec($sql);
				if($result==1){
					echo "<script> alert('添加成功'); </script>";
					echo "<meta http-equiv='Refresh' content='0;URL=manage_module.php'>";
				}else{
					exit('数据库出错！9');
				}
			}else{
				echo "<script> alert('所添加已经存在'); </script>";
				echo "<meta http-equiv='Refresh' content='0;URL=manage_module.php'>";
				
			}
		}else if($op==3){ //帖子查看详情
			$id=$_POST['id'];
			$sql="select * from post where Id=$id";
			$data=$mysql->exec($sql);
			echo json_encode($data);
		}else if($op==5){ // 帖子取消禁止
			$id=$_POST['id'];
			$sql="update post set audit_result=1 , reason=0 where Id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}else if($op==7){ // 帖子撤销举报
			$id=$_POST['id'];
			$sql="update post set inform=0 where  Id =$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}
	}
	
?>
