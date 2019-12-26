<?php
	/* 数据插入 */
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/Forum_2_1_php.php';
	//获取当前用户
	$user_id=$_SESSION['id'];
	
	$mysql=new Mysql();
	$post_user_id=$_GET['post_user_id'];
	if($user_id==$post_user_id){
		header("Location:Forum_user.php");
	}
	
	//用户
	$sql="select * from `user` where `id`=$post_user_id";
	$user=$mysql->queryOne($sql);
	//帖子
	$sql="select * from `post` where `post_user_id`=$post_user_id and `audit_result`=1 order by 'create_time' desc";
	$data=$mysql->queryAll($sql);
	//数据缓存
	$dataLength=count($data);//数组长度
	$toLoad_sum=ceil($dataLength/2);//最大加载次数
	$more=1;//more=1说明数据未缓存完
	$sum=isset($_GET['sum'])?$_GET['sum']:'1';//默认加载次数 1 次
	if($sum>$toLoad_sum)$sum=$toLoad_sum;//限制sum不超过最大加载
	$toLoad=2;//加载一次多显示一条内容
	$content=$sum*$toLoad;//加载的内容条数
	$data=array_slice($data,0,$content);
	if($content>=$dataLength){
		//more=0说明数据已缓存完
		$more=0;
	}
	
	$sql="select * from `commodity` where user_id=$post_user_id and `audit`=1 order by `create_time` desc";
	$product=$mysql->queryAll($sql);
	//数据缓存_商品
	$productLength=count($product);//数组长度
	$toLoad_sum_1=ceil($productLength/5);//最大加载次数
	$more_1=1;//more_1=1说明数据未缓存完
	$sum_1=isset($_GET['sum_1'])?$_GET['sum_1']:'1';//默认加载次数 1 次
	if($sum_1>$toLoad_sum_1)$sum_1=$toLoad_sum_1;//限制sum不超过最大加载
	$toLoad_1=5;//加载一次多显示5条内容
	$content_1=$sum_1*$toLoad_1;//共加载的内容条数
	$product=array_slice($product,0,$content_1);
	if($content_1>=$productLength){
		//more_1=0说明数据已缓存完
		$more_1=0;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>贴吧模式</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<script src="assets/js/jquery-1.11.1.js"></script>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/all_style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/user_style.css">
</head>
<body>
	<?php include "header.html" ?>
	<!-- 导航栏 -->
	<div id="nav" class="type_area">
		<ul class="nav_item">
			<li onclick="location.href='index.html'">首页</li>
			<li onclick="location.href='Forum_2.php'">贴吧模式</li>
			<li onclick="location.href='Forum_module.php'">帖子分类</li>
			<li onclick="location.href='Forum_post.php'">发帖</li>
			<li onclick="location.href='Forum_store.php'">商城</li>
			<li onclick="location.href='Forum_user.php'">个人中心</li>
			<li class="nav_itemEnd">快捷导航</li>
		</ul>
		<ul class="user_item">
			<li onclick="location.href='Forum_user.php'"><img src="assets/img/head.png" ></li>
			<li class="username" onclick="location.href='Forum_user.php'">Lin</li>
			<li><a href="Forum_2.php?type=4">我的帖子</a></li>
			<li><a href="Forum_user.php">个人资料</a></li>
			<li><a href="Forum_post.php">发帖</a></li>
		</ul>
	</div>
	<div class="path">
		<a href="javascript:history.back(-1)">查看帖子</a> > <a href="#">用户信息</a>
	</div>
	<!-- content模块 -->
	<div id="content" class="type_area">
		<div class="top">
			<div class="left">
				<div class="img">
					<img src="<?php echo $user['user_image']?>" alt="头像">
				</div>
			</div>
			<div class="text">
				<h2><?php echo $user['username'] ?></h2>
				<p>性别：<span><?php echo $user['gender'] ?></span></p>
				<div class="intro">
					<span>个人简介：</span>
					<p><?php echo $user['user_intro'] ?></p>
				</div>
			</div>
		</div>
		<div class="bottom">
			<div class="nav"><span>动态</span></div>
			<div class="content">
				<div class="nav_item">
					<table class="table table-hover table-condensed">
						<thead>
						</thead>
						<tbody>
							<?php foreach($data as $value){?>
							<tr onclick="location.href='Forum_2_1.php?id=<?php echo $value['id']?>'">
								<td><?php echo $value['post_title']?></td>
								<td><?php echo $value['post_module_name']?></td>
								<td><?php echo $value['post_user_name']?></td>
								<td><?php echo $value['post_sum']?></td>
								<?php 
									$post_id=$value["id"];
									$sql="select * from `comment` where `post_id`='$post_id' order by create_time desc limit 0, 1 ";
									$result=$mysql->queryOne($sql);
								?>
								
								<td>
									<p class="username"><?php echo isset($result['user_name'])?$result['user_name']:"-"?></p>
									<p><?php echo isset($result['create_time'])?$result['create_time']:"-"?><p>
								</td>
								
								<td><?php echo $value['post_time']?></td>
							<?php }?>
							</tr>
						</tbody>
					</table>
					<div class="more">
						<a href='javascript:viod(0)' onclick="go(<?php echo $sum+1?>)">
							<?php if($more==1)echo '显示更多';else echo '已全部加载';?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom" id="show">
			<div class="nav"><span>发布的商品</span></div>
			<div class="content">
				<div class="nav_item">
				<ul>
					<?php foreach($product as $value){?>
					<li onclick="location.href='Forum_product_info.php?id=<?php echo $value['id']?>'">
						<a href="#">
							<img src="<?php echo $value['commodity_cover_image'] ?>" alt="">
						</a>
						<p><?php echo $value['commodity_title'] ?></p>
						<span>￥<?php echo $value['commodity_price'] ?></span>
						<span><a href="#"><?php echo $value['create_time'] ?> </a>
						</span>
					</li>
					<?php } ?>
				</ul>
					<div class="more more_1">
						<a href='javascript:viod(0)' onclick="go_1(<?php echo $sum_1+1?>)">
							<?php if($more_1==1)echo '显示更多';else echo '已全部加载';?>
						</a>
					</div>
				</div>
			</div>
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
		<input type="hidden"id="post_user_id" value="<?php echo $post_user_id?>"><!-- 用于js数据调用 -->
	</div>
	<script>
		$(function(){
			/* 用户名 */
			$.ajax({
				url:"index.php",
				type:"POST",
				data:{data:'user'},
				success: function(msg){
					if(msg==1){
						window.location.href="login.html";
					}else{
						var data=JSON.parse(msg);
						$("#username").text(data['username']);
						$(".user_item li img").attr('src',data['user_image']);
						$(".user_item .username").text(data['username']);
					}
				},
			    error:function(e){
			    	alert("前后端交互失败！"); 
			    }
			})
		});
		//模仿数据缓冲
		function go(sum){
			post_user_id=$("#post_user_id").val();
			$(".more_0 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?post_user_id="+post_user_id+"&sum="+sum;
			},1000);
		}
		function go_1(sum){
			post_user_id=$("#post_user_id").val();
			$(".more_1 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?post_user_id="+post_user_id+"&sum_1="+sum+"#show";
			},1000);
		}
	</script>
</body>
</html>