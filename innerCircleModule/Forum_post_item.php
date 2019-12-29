<?php
	header("content-type:text/html;charset=utf-8"); 
	/* 数据插入 */
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	//include $pach.'/Forum/innerCircleModule/Forum_post_item_service.php';
	session_start();
	if(isset($_SESSION['u_id'])){
		$comment_user_id=$_SESSION['u_id'];
	}else{
		header("Location:login.html");
	}


	$Id=$_GET['Id'];
	$mysql=new Mysql();
	
	
	/* 数据查询 */
	//点击的帖子数据
	$globalPostData=$mysql->exec("select * from post where Id='$Id'");
	/* 判断帖子是否过审 */
	if($globalPostData['audit']==0||$globalPostData['audit_result']==0){
		exit("该贴未审核或者审核未通过！");
	}

	$u_id=$globalPostData['post_user_id'];
	$post_user=$mysql->exec("select * from user where u_id=$u_id");


	$comment_one=$mysql->queryAll("select * from comment where post_id='$Id' and grade=1");
	$comment_one=json_decode($comment_one);

	$comment_two=$mysql->queryAll("select * from comment where post_id='$Id' and grade=2");
	$comment_two=json_decode($comment_two);

	$comment_three=$mysql->queryAll("select * from comment where post_id='$Id' and grade=3 order by comment_time");
	$comment_three=json_decode($comment_three);


	
	/* 判断看帖用户是否已对该帖点赞 */
	$praised_post_id=$globalPostData['Id'];
	$sql="select * from praise where u_id=$u_id and praised_post_id=$praised_post_id";
	$result=$mysql->exec($sql);
	if($result){
		$praise=1;
	}else{
		$praise=2;
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>贴吧模式</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<script src="../assets/js/jquery-1.11.1.js"></script>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="../assets/bootstrap/js/bootstrap.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/index_style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/all_style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/Forum_2_1_style.css">
	
</head>
<body>
	<!-- 给ajax调用 -->
	<input id="post_id" type="hidden" value="<?php echo $globalPostData['Id'] ?>">
	<input id="u_id" type="hidden" value="<?php echo $comment_user_id ?>">
	<?php include "../header.html" ?><!-- 导入头部 -->
	<!-- 导航栏 -->
	<div id="nav" class="type_area">
		<ul class="nav_item">
			<li onclick="location.href='../index.html'">首页</li>
			<li onclick="location.href='innerCore.php'" style="background:#2d004e;">贴吧模式</li>
			<li onclick="location.href='Forum_post.php'">发帖</li>
			<li onclick="location.href='Forum_user.php'" >个人中心</li>
			<li class="nav_itemEnd">快捷导航</li>
		</ul>
		<ul class="user_item">
			<li onclick="location.href='Forum_user.php'"><img src="../assets/img/head.png" ></li>
			<li class="username" onclick="location.href='Forum_user.php'">Lin</li>
			<li><a href="innerCore.php?type=4">我的帖子</a></li>
			<li><a href="Forum_user.php">个人资料</a></li>
			<li><a href="Forum_post.php">发帖</a></li>
			<li><a href="javascript:history.back(-1)">返回</a></li>
		</ul>
	</div>
	<!-- content模块 -->
	<div id="content" class="type_area">
		<div class="content_body">
			<div class="content_template">
				<div class="template_left">
					<div class="img" ><img src="<?php echo $post_user['u_image'] ?>" alt="用户头像"></div>
					<div class="text">
						<p><?php echo $post_user['u_name']?></p>
						<p>性别：<?php echo $post_user['u_sex']?></p>
						<p>累计获赞：<?php echo $post_user['u_hot']?></p>
					</div>
				</div>
				<div class="template_right">
					<div class="title">
						<h3>
							<?php echo $globalPostData['post_title']?>
							<a href="#" id="inform" onclick="inform(<?php echo $globalPostData['Id'] ?>)">举报该贴</a>
						</h3>
					</div>
					
					<div class="content">
						<?php echo $globalPostData['post_comment']?>
					</div>
					<div class="img <?php if($praise==1)echo 'is_praise'?>" title="点赞+1" id="praise"></div>
					<span class="praise_sum"><?php echo $globalPostData['post_hot'] ?></span>
					<div class="bottom">
						<span class="create_time"><?php echo $globalPostData['post_time']?></span>&nbsp;&nbsp;
						<a href="#container" >回复</a>
					</div>


				</div>
			</div>
			<!-- 板块 -->
			<?php 
			foreach($comment_one as $first_comment_data){ 

				$user_id=$first_comment_data->{'comment_user_id'};
				/* 找出评价的那个人*/
				$first_comment_user=$mysql->exec("select * from user where u_id=$user_id");

			?>
			<div class="content_template" id="template_<?php echo $first_comment_data->{'id'} ?>"><!-- id作为动态的 锚点 -->
				<div class="template_left">
					<div class="template_left">

						<div class="img" onclick="go(<?php echo $first_comment_user['u_id'] ?>)">
							<img src="<?php echo $first_comment_user['u_image']?>" alt="用户头像">			
						</div>
						<div class="text">
							<p><?php echo $first_comment_user['u_name']?></p>
							<p>性别：<?php echo $first_comment_user['u_sex']?></p>
							<p>累计获赞：<?php echo $first_comment_user['u_hot']?></p>
						</div>
					</div>
				</div>
				<div class="template_right">
					<div class="content">
						<?php echo $first_comment_data->{'content'}?>
					</div>
					<div class="bottom">
						<span class="create_time">
							<?php echo $first_comment_data->{'comment_time'}?>
						</span>&nbsp;&nbsp;
						<!-- 查询是否有二级评论 若没有 创建回复按钮  -->
						<?php	
						$temporyParm=$first_comment_data->{'id'};
						$multipeCommentCount=$mysql->exec("select COUNT(*) as sum from comment where father_id='$temporyParm' and (grade=2 or grade=3)");
							if($multipeCommentCount['sum']==0){
						?>

						<a href="#0"data-toggle="collapse" 
							data-target="#dome_<?php echo $first_comment_data->{'id'}?>" >回复

						</a>&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" href=".tow_<?php echo $first_comment_data->{'id'} ?>" aria-expanded="true" >
							收起回复
						</a>

					</div>
					<div class="collapse"id="dome_<?php echo $first_comment_data->{'id'} ?>">
						<form action="Forum_post_item_service.php" method="post">
								<!--<input type="hidden" name="comment_user_id" value="<?php echo $comment_user_id ?>">-->
								<input type="hidden" name="father_id" value="<?php echo $first_comment_data->{'id'}?>">
								<input type="hidden" name="post_id" value="<?php echo $first_comment_data->{'post_id'}?>">
								<input type="hidden" name="reply_u_id" value="<?php echo $first_comment_data->{'comment_user_id'}?>">
								<input type="hidden" name="grade" value="2">
								<textarea class="form-control" rows="3" name="content" placeholder="回复..."></textarea>
								<button class="btn btn-default btn-sm float_right" role="button" type="submit">发送</button>
						</form>
					</div>
					<!-- 二级评论 -->
					<?php foreach($comment_two as $second_comment_data){
						if($second_comment_data->{'father_id'}==$first_comment_data->{'id'}){
					?>
					<div class="tow_<?php echo $first_comment_data->{'id'} ?> comment collapse in" id="">
						<div class="comment_two">
							<div class="comment_img"><img src="<?php  //通过comment表user_name获取到user表的用户信息
								$comment_user_name=$second_comment_data->{'comment_user_name'};
								$userone=$mysql->exec("select * from user where u_name='$comment_user_name'");
								echo $userone['u_image'];
							 ?>"></div>
							<div class="text">
								<span><?php echo $second_comment_data->{'comment_user_name'}?></span>&nbsp;:
								<span><?php echo $second_comment_data->{'content'}?></span>
								<div class="bottom"><span class="create_time"><?php echo $second_comment_data->{'comment_time'}?></span>&nbsp;&nbsp;
								<a href="#template_<?php echo $first_comment_data->{'id'}?>" data-toggle="collapse" data-target="#dome1_<?php echo $second_comment_data->{'id'}?>">回复</a>
							</div>
							</div>
							<div class="collapse"id="dome1_<?php echo $second_comment_data->{'id'}?>">
								<form action="Forum_post_item_service.php" method="post">
										<!--<input type="hidden" name="comment_user_id" value="<?php echo $comment_user_id ?>">-->
										<input type="hidden" name="father_id" value="<?php echo $second_comment_data->{'id'}?>">
										<input type="hidden" name="post_id" value="<?php echo $second_comment_data->{'post_id'}?>">
										<input type="hidden" name="reply_u_id" value="<?php echo $second_comment_data->{'comment_user_id'}?>">
										<input type="hidden" name="grade" value="3">
										<textarea class="form-control" rows="3" name="content" placeholder="回复..."></textarea>
										<button class="btn btn-default btn-xs float_right" role="button" type="submit">发送</button>
								</form>
							</div>
						</div>
						<!-- 三级评论 -->
						<?php 
						//$reply_u_id=$second_comment_data->{'u_id'};
						foreach($comment_three as $third_comment_data){ 

							if($third_comment_data->{'father_id'}==$second_comment_data->{'id'}){
						?>
						<div class="comment_three">
							<div class="comment_img">
								<img src="<?php  //通过comment表user_name获取到user表的用户信息
									$comment_user_name=$third_comment_data->{'comment_user_name'};
									$userone=$mysql->exec("select * from user where u_name='$comment_user_name'");
									
									echo $userone['u_image'];
								 ?>">
							</div>
							<div class="text">
								<span>
									<?php echo $third_comment_data->{'comment_user_name'}?>
								</span>
								<span>回复</span>
								<span><?php //通过id查找回复的人的名字
									$temporyParm=$third_comment_data->{'reply_u_id'};
									$reply_user=$mysql->exec("select * from user where u_id=$temporyParm");
									echo $reply_user['u_name'];
									
								?></span>&nbsp;:
								<span><?php echo $third_comment_data->{'content'}?></span>
								<div class="bottom"><span class="create_time"><?php echo $third_comment_data->{'comment_time'}?></span>&nbsp;&nbsp;
								<a href="#template_<?php echo $first_comment_data->{'id'}?>"data-toggle="collapse"data-target="#<?php echo 'id_'.$third_comment_data->{'id'} ?>">回复</a></div>
							</div>
							<div class="collapse" id="id_<?php echo $third_comment_data->{'id'}?>">
								<form action="Forum_post_item_service.php" method="post">
									<!--<input type="hidden" name="comment_user_id" value="<?php echo $comment_user_id ?>">-->
									<input type="hidden" name="father_id" value="<?php echo $third_comment_data->{'id'}?>">
									<input type="hidden" name="post_id" value="<?php echo $third_comment_data->{'post_id'}?>">
									<input type="hidden" name="reply_u_id" value="<?php echo $third_comment_data->{'comment_user_id'}?>">
									<input type="hidden" name="grade" value="3">
									<textarea class="form-control" rows="3" name="content" placeholder="回复..."></textarea>
									<button class="btn btn-default btn-xs float_right" role="button" type="submit">发送</button>
								</form>
							</div>
						</div>
						<?php } } ?>
					</div>
					<?php } }?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="textarea">
			<form action="Forum_post_item_service.php" method="post">
				<script id="container" name="content" type="text/plain" style="height: 150px;">
				</script>
				<!--第一项评论者的id，为论坛登陆者本人的id -->
				<!--<input type="hidden" name="comment_user_id" value="<?php echo $comment_user_id ?>">-->
				<!--第二项为所评论的帖子的id-->
				<input type="hidden" name="post_id" value="<?php echo $globalPostData['Id']?>">
				<!--第三项为所评论的帖子的父级别id-->
				<input type="hidden" name="father_id" value="0">
				<!--第四项为所评论的评论发起者的id-->
				<input type="hidden" name="reply_u_id" value="<?php echo $globalPostData['post_user_id']?>">
				<!--第五项为评论的帖子的级别-->
				<input type="hidden" name="grade" value="1">
				<button class="btn btn-primary btn-block" role="button" type="submit">发送</button>
			</form>
		</div>
	</div>
	<!-- 底部模块 -->
	<div id="footer" class="type_area">
		<div class="footer_statistics">
			在线人数 - 统计 4 人在线
		</div>
		<div class="foot_text">
			<p>&copy;2019 李升典 童观锐 .AllRightsReserved</p>
		</div>
	</div>
</body>
	<script type="text/javascript" src="../assets/ueditor/ueditor.config.js"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.all.js"></script>
	<!-- 语言类型 -->
	<script type="text/javascript" src="../assets/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!-- 实例化编辑器 -->
	<script type="text/javascript">
		var ue = UE.getEditor('container', {
			toolbars: [
				['emotion','bold','insertimage','undo','redo','cleardoc']
			]
		});
		$(function(){
			$("#praise").click(function(){
				$.ajax({
					url:"Forum_post_item_service.php",
					type:"get",
					data:{'praised_post_id':$("#post_id").val(),'praised_user_id':$("#u_id").val(),'op':1},
					success: function(msg){
						if(msg==0){
							alert("您已点赞。");
						}else if(msg==1){
							alert("点赞成功！");
							location.href="Forum_post_item.php?Id="+$("#post_id").val();
						}else{
							alert("服务繁忙，请稍后再试");
						}
					},
					error:function(e){
						alert("前后端交互失败！"); 
					}
				});
			});
			/* 用户名 */
			$.ajax({
				url:"../index.php",
				type:"POST",
				data:{data:'user'},
				success: function(msg){
					if(msg==1){
						window.location.href="../login.html";
					}else{
						var data=JSON.parse(msg);
						$("#username").text(data['u_name']);
						if (data['u_image']!=null) {
							$(".user_item li img").attr('src',data['u_image']);
						}	
						$(".user_item .username").text(data['u_name']);
					}
				},
			    error:function(e){
			    	alert("前后端交互失败！"); 
			    }
			})
			/* 公告 */
			$.ajax({
				url:"../index.php",
				type:"POST",
				data:{data:'notice'},
				success: function(msg){
					var data=JSON.parse(msg);
					$(".notice_p").text("公告："+data['notice']);
			    },
			    error:function(e){
			    	alert("前后端交互失败！"); 
			    }
			})
		});
		//点击头像触发事件
		function go(post_u_id){
			window.location.href="Forum_user.php?u_id="+post_u_id;
		}
		/* 举报 */
		function inform(id){
			if(confirm("是否举报该帖子？")){
				$.ajax({
					url:"Forum_user_service.php",
					type:"GET",
					data:{op:'2',id:id},
					success: function(msg){
						if(msg==1){
							alert("举报成功，等待管理员处理。");
						}else{
							alert("请勿重复提交");
						}
				    },
				    error:function(e){
				    	alert("前后端交互失败！"); 
				    }
				})
			}
		}
	</script>
	
</html>