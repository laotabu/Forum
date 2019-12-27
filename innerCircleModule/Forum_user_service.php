<?php
	header("Content-type:text/html;charset-utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'../Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	//获取当前用户
	session_start();
	if(isset($_SESSION['u_id'])){
		$u_id=$_SESSION['u_id'];
	}else{
		header("Location:../login.html");
	}
	
	if(!empty($_GET)){
		$op=isset($_GET['op'])?$_GET['op']:'';
		if($op==1){//删除帖子
			$post_id=$_GET['id'];
			$sql="delete from post where Id=$post_id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}/*else if($op==2){
			$id=$_GET['id'];
			$sql="delete from `commodity` where id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}else if($op==3){ 商品举报 
			$id=$_GET['id'];
			$sql="update `commodity` set inform=$u_id where id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}*/else if($op==4){/* 帖子举报 */
			$id=$_GET['id'];
			$sql="update `post` set inform=$u_id where id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			}
		}
	}
	if(!empty($_POST)){
		$op=isset($_POST['op'])?$_POST['op']:'';
		if($op==1){//更改头像
			//图片上传
			if(isset($_FILES['img'])&&$_FILES['img']['error']==0){
				//判断上传的文件格式
				if(!($_FILES['img']['type']=="image/png"||$_FILES['img']['type']=="image/jpg")){
					echo "<script>
							alert('封面格式不正确！请重新上传！');
							location.href='Forum_user.php';
						</script>";
					exit();
				};
				$path='../assets/uploads/'.time().'.png';
				if(move_uploaded_file($_FILES['img']['tmp_name'], $path)){
					$sql="select * from user where u_id=$u_id";
					$data=$mysql->exec($sql);
					$old_pach=$data['u_image'];
					 
					$sql="update user set u_image='$path' where u_id =$u_id";
					$result=$mysql->exec($sql);
					if($result==1){
						if($old_pach=="../assets/uploads/head.png"){//若原头像不是默认头像 则删除原头像
							echo "<script>
								alert('头像修改成功！');
								location.href='Forum_user.php';
							</script>";
						}else{
							if(unlink($old_pach)){
								echo "<script>
									alert('头像修改成功！');
									location.href='Forum_user.php';
								</script>";
							}else{
								echo "<script>
									alert('头像修改成功！原头像删除失败！');
									location.href='Forum_user.php';
								</script>";
							}
						}				
						
					}else{
						echo "<script>
							alert('数据库出错！');
							location.href='Forum_user.php';
						</script>";
					}
				}else{
					echo "存储图片失败！";
				}
			}
		}else if($op==2){//编辑个人信息
			$u_sex=$_POST['u_sex'];
			$u_info=$_POST['u_info'];
			$u_address=$_POST['u_address'];
			$u_phone=$_POST['u_phone'];
			/* 检测手机号是否存在 */
			$sql="select * from user where u_phone=$u_phone and u_id!=$u_id";
			$result=$mysql->exec($sql);
			echo $result;
			if($result){
				echo "<script>
					alert('手机号已被注册，请重新填写。');
					window.history.back(-1);
				</script>";
				exit;
			}
			$sql="update user set u_sex='$u_sex',u_phone='$u_phone',u_address='$u_address', u_info='$u_info' where u_id =$u_id;";
			$result=$mysql->exec($sql);
			if($result==1){
				echo "<script>
					alert('保存成功！');
					location.href='Forum_user.php';
				</script>";
			}else{
				echo "<script>
					alert('请修改内容再保存！');
					location.href='Forum_user.php';
				</script>";
			}
		}/*else if($op==3){//发布商品
			
			if(isset($_FILES['commodity_cover_image'])&&$_FILES['commodity_cover_image']['error']==0){
				//判断上传的文件格式
				if(!($_FILES['commodity_cover_image']['type']=="image/png"||$_FILES['commodity_cover_image']['type']=="image/jpg")){
					exit("封面格式不正确！请重新上传！");
				};
				$commodity_title=$_POST['commodity_title'];
				$commodity_price=$_POST['commodity_price'];
				$commodity_details=$_POST['commodity_details'];
				$commodity_type=$_POST['commodity_type'];
				$pickup_way=$_POST['pickup_way'];
				$post_id=$_POST['post_id'];
				
				$path='./assets/commodity_images/'.time().'.png';
				if(move_uploaded_file($_FILES['commodity_cover_image']['tmp_name'], $path)){
						//插入数据
						$sql="insert into `commodity`(`u_id`,`commodity_title`,`commodity_cover_image`,`commodity_price`,`commodity_details`,`commodity_type`,`pickup_way`,`post_id`)value('$u_id','$commodity_title','$path','$commodity_price','$commodity_details','$commodity_type','$pickup_way','$post_id')";
						$result=$mysql->exec($sql);
						if($result==1){
							echo "<script>
								alert('发布成功，等待管理员审核。');
								location.href='Forum_user.php';
							</script>";
						}else{
							echo "<script>
								alert('数据库出错！');
								location.href='Forum_user.php';
							</script>";
						}
				}
			}else{
				echo "请上传封面！";
			}
		}else if($op==4){//返回点击目标的商品信息
			$id=$_POST['id'];
			$sql="select * from `commodity` where id = $id";
			$data=$mysql->exec($sql);
			echo json_encode($data);
		}else if($op==5){//编辑商品
			if(isset($_FILES['commodity_cover_image'])&&$_FILES['commodity_cover_image']['error']==0){ //有上传封面
				//判断上传的文件格式
				if(!($_FILES['commodity_cover_image']['type']=="image/png"||$_FILES['commodity_cover_image']['type']=="image/jpeg")){
					exit("封面格式不正确！请重新上传！");
				};
				$commodity_id=$_POST['id'];
				$commodity_title=$_POST['commodity_title'];
				$commodity_price=$_POST['commodity_price'];
				$commodity_details=$_POST['commodity_details'];
				$commodity_type=$_POST['commodity_type'];
				$pickup_way=$_POST['pickup_way'];
				$post_id=$_POST['post_id'];
				
				$path='./assets/commodity_images/'.time().'.png';
				if(move_uploaded_file($_FILES['commodity_cover_image']['tmp_name'], $path)){
						//更新数据
						$sql="update `commodity` set commodity_title='$commodity_title',commodity_price='$commodity_price',commodity_details='$commodity_details',pickup_way='$pickup_way',commodity_type='$commodity_type',commodity_cover_image='$path',post_id='$post_id' where id ='$commodity_id'";
						$result=$mysql->exec($sql);
						if($result==1){
							//修改审核
							$sql="update `commodity` set audit='2' where id ='$commodity_id';";
							$result=$mysql->exec($sql);
							echo "<script>
								alert('修改成功，等待管理员审核。');
								location.href='Forum_user.php';
							</script>";
						}else{
							echo "<script>
								alert('数据库出错！修改失败');
								location.href='Forum_user.php';
							</script>";
						} 
				}
			}else{//没有上传封面
				$commodity_id=$_POST['id'];
				$commodity_title=$_POST['commodity_title'];
				$commodity_price=$_POST['commodity_price'];
				$commodity_details=$_POST['commodity_details'];
				$commodity_type=$_POST['commodity_type'];
				$pickup_way=$_POST['pickup_way'];
				$post_id=$_POST['post_id'];

				$sql="update `commodity` set commodity_title='$commodity_title',commodity_price='$commodity_price',commodity_details='$commodity_details',pickup_way='$pickup_way',commodity_type='$commodity_type',post_id='$post_id' where id ='$commodity_id'";
						$result=$mysql->exec($sql);
						if($result==1){
							//修改审核
							$sql="update `commodity` set audit='2' where id ='$commodity_id';";
							$result=$mysql->exec($sql);
							echo "<script>
								alert('修改成功，等待管理员审核。');
								location.href='Forum_user.php';
							</script>";
						}else{
							echo "<script>
								alert('数据未更新！');
								location.href='Forum_user.php';
							</script>";
						} 
			}
		}else if($op==6){
			$id=$_POST['id'];
			$sql="update `commodity` set `commodity_buy`=$u_id where id=$id";
			$result=$mysql->exec($sql);
			if($result==1){
				echo 1;
			}else{
				echo 2;
			} 
		}*/
	}
?>
