<?php
	header("content-type:text/html;charset=utf-8"); 
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	include $pach.'/Forum/utils/time_func.php';
	$mysql=new Mysql();
	if (!empty($_POST)){
		$u_id=isset($_POST['u_id'])?$_POST['u_id']:'';
		$u_passwd=isset($_POST['u_passwd'])?$_POST['u_passwd']:'';
		$u_passwd_error=isset($_POST['u_passwd_error'])?$_POST['u_passwd_error']:'';
		$sql="select * from user where u_id='$u_id' and u_passwd='$u_passwd'";
		$result=$mysql->exec($sql);
		//查询账号密码是否存在并正确

		if($result!=null){
			
			$is_enable=$result['is_enable'];
			$last_login_time=$result['last_login_time'];
			//判断锁定时间
			if($is_enable==0){ //密码错误锁定
				//获取最后一次登陆失败的时间的秒数
				if ($last_login_time==null) {
					$last_login_time = 0;
				}else{
					$last_login_time=DatetimeToSeconds("$last_login_time");//引入封装的函数
				}
				//获取现在时间的秒数
				$now_time=time();
				$interval=$now_time-$last_login_time;
				if($interval>60){
					$sql="update user set is_enable=1,u_passwd_error=0 where u_id ='$u_id'";
					$affectedRows=$mysql->exec($sql);
					if($affectedRows==1){
						//储存用户会话信息
						session_start();
						
						$_SESSION['u_name'] = $result['u_name'];
						$_SESSION['u_id'] = $result['u_id'];
						$_SESSION['u_passwd'] = $result['u_passwd'];
						$_SESSION['u_type'] = $result['u_type'];
						//进入哪个系统
						if($result['u_type']==1){
							echo 1;
						}else if($result['u_type']==2){
							echo 2;
						}
					}else{
						echo "系统出错！";
					}
				}else{
						$relieve=$last_login_time+60;

						$relieve=date('Y-m-d H:i:s',$relieve);
						echo "账号锁定至".$relieve;
				}
			}else if($is_enable==1){ //账号正常
				//清除密码错误次数
				$sql="update user set u_passwd_error=0 where u_id='$u_id'";
				$affectedRows=$mysql->exec($sql);
				//存储上次登录时间
				date_default_timezone_set("PRC");//设置时区
				$login_time = date("Y-m-d h:i:s");
				$sql="update user set last_login_time='$login_time' where u_id='$u_id'";
				$mysql->exec($sql);
				
				//储存用户会话信息
				session_start();
				$_SESSION['u_name'] = $result['u_name'];
				$_SESSION['u_id'] = $result['u_id'];
				$_SESSION['u_passwd'] = $result['u_passwd'];
				$_SESSION['u_type'] = $result['u_type'];
				//进入哪个系统
				if($result['u_type']==1){
					echo 1;
				}else if($result['u_type']==2){
					echo 2;
				}
			}else if($is_enable==2){ //管理员锁定
				echo 3;
			}
		}else{
			//账号不存在

			$sql="select count(*) as sum from user where u_id='$u_id'";
			$result=$mysql->exec($sql);

			if($result['sum']==0){
				echo '用户名不存在，注册用户名或重新输入！';
				exit;
			}else{ //密码错误
				$sql="select * from user where u_id='$u_id'";
				$result=$mysql->exec($sql);	
				$is_enable=$result['is_enable'];
				$last_login_time=$result['last_login_time'];
				//判断锁定时间
				if($is_enable==0){
					//获取当前时间的秒数
					if ($last_login_time==null) {
						$last_login_time = 0;
					}else{
						$last_login_time=DatetimeToSeconds("$last_login_time");//引入封装的函数
					}
					$now_time=time();
					$interval=$now_time-$last_login_time;
					if($interval>60){
						$sql="update user set is_enable=1, u_passwd_error=0 where u_id='$u_id'";
						$affectedRows=$mysql->exec($sql);
						if($affectedRows==1){

							//储存用户会话信息
							session_start();

							$_SESSION['u_name'] = $result['u_name'];
							$_SESSION['u_id'] = $result['u_id'];
							$_SESSION['u_passwd'] = $result['u_passwd'];
							$_SESSION['u_type'] = $result['u_type'];
							//进入哪个系统
							if($result['u_type']==1){
								echo 1;
							}else if($result['u_type']==2){
								echo 2;
							}
						}else{
							echo "系统出错！";
						}
					}else{
							$relieve=$last_login_time+60;
							$relieve=date('Y-m-d H:i:s',$relieve);
							echo "账号锁定至".$relieve;
					}
				}else if($is_enable==2){
					echo 3;
				}else{
					$u_passwd_error=$result['u_passwd_error'];
					if($u_passwd_error<3){
						$u_passwd_error+=1;
						$sql="update user set u_passwd_error='$u_passwd_error' where u_id='$u_id'";
						$affectedRows=$mysql->exec($sql);
						if($affectedRows==1){
							$num=3-$u_passwd_error;
							echo "密码输入错误！今日还可以输入".$num."次错误";
						}
					}else{
						$sql="update user set is_enable=0 where u_id='$u_id'";
						$affectedRows=$mysql->exec($sql);
						if($affectedRows==1){
							$last_login_time=date('Y-m-d H:i:s', time());
							$sql="update user set last_login_time='$last_login_time' where u_id='$u_id'";
							if($mysql->exec($sql)==1){
								echo "账户被锁定，24小时后自动解锁";
							}
							
						}
					}
				}
			}
			
			
		}
	}
?>
