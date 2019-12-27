<?php
	header("content-type:text/html;charset=utf-8"); 
	/* 数据插入 */
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	//include $pach.'/Forum/innerCircleModule/Forum_post_item_service.php';
	$Id=$_GET['Id'];
	$mysql=new Mysql();
	
	
	/* 数据查询 */
	//点击的帖子数据
	$sql="select * from post where Id='$Id'";
	$post=$mysql->exec($sql);
	/* 判断帖子是否过审 */
	if($post['audit']==0||$post['audit_result']==0){
		exit("该贴未审核或者审核未通过！");
	}
	/*链接商品的数据*/
	/*$post_id=$post['Id'];
	$sql="select * from `commodity` where post_id=$post_id and `audit`=1 ORDER BY `create_time` desc limit 1";
	$product_link=$mysql->exec($sql);
	if($product_link){
		$product_id=$product_link['id'];//product_id==n -> 所链接商品的id为 n
	}else{
		$product_id=0; //product_id==0 -> 没有商品链接上 （帖子下边判断是否存在product_id而渲染a标签  144）
	}*/

	$u_id=$post['post_user_id'];
	$sql="select * from user where u_id=$u_id";
	$user=$mysql->exec($sql);
	$sql="select * from comment where post_id='$Id' and grade=1";
	$comment_one=$mysql->queryAll($sql);
	$comment_one=json_decode($comment_one);
	$sql="select * from comment where post_id='$Id' and grade=2";
	$comment_two=$mysql->queryAll($sql);
	$comment_two=json_decode($comment_two);
	$sql="select * from comment where post_id='$Id' and grade=3 order by create_time";
	$comment_three=$mysql->queryAll($sql);
	$comment_three=json_decode($comment_three);
	/* 判断看帖用户是否已对该帖点赞 */
	$praised_post_id=$post['Id'];
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
	<input id="post_id" type="hidden" value="<?php echo $post['Id'] ?>">
	<input id="u_id" type="hidden" value="<?php echo $user['u_id'] ?>">
	<?php include "../header.html" ?><!-- 导入头部 -->
	<!-- 导航栏 -->
	<div id="nav" class="type_area">
		<ul class="nav_item">
			<li onclick="location.href='../index.html'">首页</li>
			<li onclick="location.href='innerCore.php'" style="background:#2d004e;">贴吧模式</li>
			<!--<li onclick="location.href='Forum_module.php?module_type=1&module_id=1'">帖子分类</li>-->
			<li onclick="location.href='Forum_post.php'">发帖</li>
			<!--<li onclick="location.href='Forum_store.php'">商城</li>-->
			<li onclick="location.href='Forum_user.php'" >个人中心</li>
			<li class="nav_itemEnd">快捷导航</li>
		</ul>
		<ul class="user_item">
			<li onclick="location.href='Forum_user.php'"><img src="../assets/img/head.png" ></li>
			<li class="username" onclick="location.href='Forum_user.php'">Lin</li>
			<li><a href="innerCore.php?type=4">我的帖子</a></li>
			<li><a href="Forum_user.php">个人资料</a></li>
			<li><a href="Forum_post.php">发帖</a></li>
		</ul>
	</div>
	<div class="path">
		<a href="javascript:history.back(-1)">返回</a>
		<a href="Forum_post.php">发帖</a>
		<a href="#container">回复</a>
	</div>
	<!-- content模块 -->
	<div id="content" class="type_area">
		<div class="content_body">
			<div class="content_template">
				<div class="template_left">
					<div class="img" onclick="go(<?php echo $user['u_id'] ?>)"><img src="<?php echo $user['u_image'] ?>" alt="用户头像"></div>
					<div class="text">
						<p><?php echo $user['u_name']?></p>
						<p>性别：<?php echo $user['u_sex']?></p>
						<p>累计获赞：<?php echo $user['u_hot']?></p>
					</div>
				</div>
				<div class="template_right">
					<div class="title">
						<h3>
							<?php echo $post['post_title']?>
							<a href="#" id="inform" onclick="inform(<?php echo $post['Id'] ?>)">举报该贴</a>
						</h3>
					</div>
					
					<div class="content">
						<?php echo $post['post_comment']?>
					</div>
					<div class="img <?php if($praise==1)echo 'is_praise'?>" title="点赞+1" id="praise"></div>
					<span class="praise_sum"><?php echo $post['post_hot'] ?></span>
					<div class="bottom"><span class="create_time"><?php echo $post['post_time']?></span>&nbsp;&nbsp;<a href="#container" >回复</a></div>

					<!--<span class="link">
						 根据product_id 判断是否有链接商品 
						<?php if($product_id!=0){?>
							<a href='Forum_product_info.php?id=<?php echo $product_id ?>'>> 商品链接</a>
						<?php } ?>
					</span>-->
				</div>
			</div>
			<!-- 板块 -->
			<?php 
			foreach($comment_one as $value){ 
			$u_id=$value->{'u_id'};
			$sql="select * from user where u_id=$u_id";
			$user=$mysql->exec($sql);

			?>
			<div class="content_template" id="template_<?php echo $value->{'id'} ?>"><!-- id作为动态的 锚点 -->
				<div class="template_left">
					<div class="template_left">
						<div class="img" onclick="go(<?php echo $user['u_id'] ?>)"><img src="<?php echo $user['u_image']?>" alt="用户头像"></div>
						<div class="text">
							<p><?php echo $user['u_name']?></p>
							<p>性别：<?php echo $user['u_sex']?></p>
							<p>累计获赞：<?php echo $user['u_hot']?></p>
						</div>
					</div>
				</div>
				<div class="template_right">
					<div class="content">
						<?php echo $value->{'content'}?>
					</div>
					<div class="bottom">
						<span class="create_time"><?php echo $value->{'create_time'}?></span>&nbsp;&nbsp;
						<!-- 查询是否有二级评论 若没有 创建回复按钮  -->
						<?php	$a=$value->{'id'};
						$sql="select COUNT(*) as sum from comment where post_id=2 and father_id='$a' and (grade=2 or grade=3)";
							$result=$mysql->exec($sql);
							if($result['sum']==0){
						?>
						<a href="#0"data-toggle="collapse"data-target="#dome_<?php echo $value->{'id'} ?>">回复</a>&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" href=".tow_<?php echo $value->{'id'} ?>" aria-expanded="true" >
							收起回复
						</a>
					</div>
					<div class="collapse"id="dome_<?php echo $value->{'id'} ?>">
						<form action="" method="post">
								<input type="hidden" name="u_id" value="<?php echo $user['u_id'] ?>">
								<input type="hidden" name="father_id" value="<?php echo $value->{'id'}?>">
								<input type="hidden" name="post_id" value="<?php echo $value->{'post_id'}?>">
								<input type="hidden" name="reply_u_id" value="<?php echo $value->{'id'}?>">
								<input type="hidden" name="grade" value="2">
								<textarea class="form-control" rows="3" name="content" placeholder="回复..."></textarea>
								<button class="btn btn-default btn-sm float_right" role="button" type="submit">发送</button>
						</form>
					</div>
					<!-- 二级评论 -->
					<?php foreach($comment_two as $value1){
						if($value1->{'father_id'}==$value->{'id'}){
					?>
					<div class="tow_<?php echo $value->{'id'} ?> comment collapse in" id="">
						<div class="comment_two">
							<div class="comment_img"><img src="<?php  //通过comment表user_name获取到user表的用户信息
								$u_name=$value1->{'u_name'};
								$sql="select * from user where u_name='$u_name'";
								$userone=$mysql->exec($sql);
								echo $userone['u_image'];
							 ?>"></div>
							<div class="text">
								<span><?php echo $value1->{'u_name'}?></span>&nbsp;:
								<span><?php echo $value1->{'content'}?></span>
								<div class="bottom"><span class="create_time"><?php echo $value1->{'create_time'}?></span>&nbsp;&nbsp;
								<a href="#template_<?php echo $value->{'id'}?>" data-toggle="collapse" data-target="#dome1_<?php echo $value1->{'id'}?>">回复</a></div>
							</div>
							<div class="collapse"id="dome1_<?php echo $value1->{'id'}?>">
								<form action="" method="post">
										<input type="hidden" name="u_id" value="<?php echo $user['u_id'] ?>">
										<input type="hidden" name="father_id" value="<?php echo $value1->{'id'}?>">
										<input type="hidden" name="post_id" value="<?php echo $value1->{'post_id'}?>">
										<input type="hidden" name="reply_u_id" value="<?php echo $value1->{'u_name'}?>">
										<input type="hidden" name="grade" value="3">
										<textarea class="form-control" rows="3" name="content" placeholder="回复..."></textarea>
										<button class="btn btn-default btn-xs float_right" role="button" type="submit">发送</button>
								</form>
							</div>
						</div>
						<!-- 三级评论 -->
						<?php 
						$reply_u_id=$value1->{'u_name'};
						foreach($comment_three as $value2){ 
							if($value2->{'father_id'}==$value1->{'id'}){
						?>
						<div class="comment_three">
							<div class="comment_img"><img src="<?php  //通过comment表user_name获取到user表的用户信息
								$username=$value2->{'u_name'};
								$sql="select * from user where u_name='$u_name'";
								$userone=$mysql->exec($sql);
								echo $userone['u_image'];
							 ?>"></div>
							<div class="text">
								<span><?php echo $value2->{'u_name'}?></span><span>回复</span>
								<span><?php //通过id查找回复的人的名字
									$id=$value2->{'reply_u_id'};
									$sql="select * from user where u_id=$id";
									$reply_user=$mysql->exec($sql);
									echo $reply_user['u_name'];
									
								?></span>&nbsp;:
								<span><?php echo $value2->{'content'}?></span>
								<div class="bottom"><span class="create_time"><?php echo $value2->{'create_time'}?></span>&nbsp;&nbsp;
								<a href="#template_<?php echo $value->{'id'}?>"data-toggle="collapse"data-target="#<?php echo 'id_'.$value2->{'id'} ?>">回复</a></div>
							</div>
							<div class="collapse" id="id_<?php echo $value2->{'id'}?>">
								<form action="" method="post">
									<input type="hidden" name="u_id" value="<?php echo $user['u_id'] ?>">
									<input type="hidden" name="father_id" value="<?php echo $value1->{'id'}?>">
									<input type="hidden" name="post_id" value="<?php echo $value2->{'post_id'}?>">
									<input type="hidden" name="reply_u_id" value="<?php echo $value2->{'u_id'}?>">
									<input type="hidden" name="grade" value="3">
									<textarea class="form-control" rows="3" name="comment" placeholder="回复..."></textarea>
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
			<form action="" method="post">
				<script id="container" name="comment" type="text/plain" style="height: 150px;">
				</script>
				<input type="hidden" name="u_id" value="<?php echo $user['u_id'] ?>">
				<input type="hidden" name="post_id" value="<?php echo $post['Id']?>">
				<input type="hidden" name="father_id" value="0">
				<input type="hidden" name="reply_u_id" value="<?php echo $post['post_user_name']?>">
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
			<p>&copy;2019 林桂鑫 林泽文 .AllRightsReserved</p>
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
					data:{'post_id':$("#post_id").val(),'u_id':$("#u_id").val(),'op':1},
					success: function(msg){
						if(msg==0){
							alert("您已点赞。");
						}else if(msg==1){
							alert("点赞成功！");
							location.href="Forum_post_item.php?id="+$("#post_id").val();
						}else{
							alert(msg);
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
						$(".user_item li img").attr('src',data['u_image']);
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
			window.location.href="Forum_post_user.php?post_u_id="+post_u_id;
		}
		/* 举报 */
		function inform(id){
			if(confirm("是否举报该帖子？")){
				$.ajax({
					url:"innerCore.php",
					type:"GET",
					data:{op:'4',id:id},
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