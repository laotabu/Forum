<?php
	date_default_timezone_set('PRC');
	/* 数据插入 */
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	//获取当前用户
	session_start();
	if(isset($_SESSION['u_id'])){
		$u_id=$_SESSION['u_id'];
	}else{
		header("Location:../login.html");
	}
	
	
	$sql="select * from user where u_id=$u_id";
	$user=$mysql->exec($sql);
	
	$sql="select * from post where post_user_id=$u_id order by post_time desc";
	$data=$mysql->queryAll($sql);
	$data = json_decode($data);
	
	//数据缓存_帖子
	$dataLength=count($data);//数组长度
	$toLoad_sum=ceil($dataLength/6);//最大加载次数
	$more=1;//more=1说明数据未缓存完
	$sum=isset($_GET['sum'])?$_GET['sum']:'1';//默认加载次数 1 次
	if($sum>$toLoad_sum)$sum=$toLoad_sum;//限制sum不超过最大加载
	$toLoad=6;//加载一次多显示6条内容
	$content=$sum*$toLoad;//共加载的内容条数
	$data=array_slice($data,0,$content);
	//print_r($data);
	if($content>=$dataLength){
		//more=0说明数据已缓存完
		$more=0;
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
    <link rel="stylesheet" type="text/css" href="../assets/css/all_style.css">
	<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/user_style.css">
</head>
<body>
	<?php include "../header.html" ?>

	<!-- 导航栏 -->
	<div id="nav" class="type_area">
		<ul class="nav_item">
			<li onclick="location.href='../index.html'">首页</li>
			<li onclick="location.href='innerCore.php'">贴吧模式</li>
			<li onclick="location.href='Forum_post.php'">发帖</li>
			<li onclick="location.href='Forum_user.php'" style="background:#2d004e;">个人中心</li>
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
	<!-- content模块 -->
	<div id="content" class="type_area">
		<div class="top">
			<div class="left">
				<div class="img">
					<img src="<?php 
						if($user['u_image']=='')
							echo '../assets/img/head.png';
						else 
							echo $user['u_image'];
					?>" alt="头像">
				</div>
				<a href="#">更换图片</a>
			</div>
			<div class="text">
				<h2><?php echo $user['u_name'] ?></h2>
				<p>学号：<span><?php echo $user['u_id'] ?></span></p>
				<p>性别：<?php if($user['u_sex']=='')echo '未填写...';else echo $user['u_sex'];?></p>
				<p>寄/ 收件地址：<?php if($user['u_address']=='')echo '未填写...';else echo $user['u_address'];?></p>
				<div class="intro">
					<span>个人简介：</span>
					<p><?php if($user['u_info']=='')echo '未填写...';else echo $user['u_info'];?></p>
				
				</div>
				<button type="button" class="btn btn-default  btn-xs float_right">
				  <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;编辑信息
				</button>
				
			</div>
		</div>
		<!-- 更换头像 -->
		<div class="new_img" style="display: none;">
			<form action="./Forum_user_service.php" method="post" enctype="multipart/form-data">
				<input type="file" class="float_left" name="img">
				<input type="hidden" name="op" value="1">
				<button type="submit" class="float_left">提交</button>
			</form>
		</div>
		<!-- 编辑个人信息 -->
		<div class="update" style="display: none;">
			<h4>个人信息编辑</h4>
			<form class="form-horizontal" action="Forum_user_service.php" method="post">
			<input type="hidden" name="op" value="2"><!-- 区别请求内容 -->
			  <div class="form-group">
				<label  class="col-sm-3 control-label">用户名</label>
				<div class="col-sm-7">
				  <input type="text" class="form-control" id="inputEmail3" placeholder="Username" value="<?php echo $user['u_name'] ?>"  disabled>
				</div>
			  </div>
			  <div class="form-group">
				<label  class="col-sm-3 control-label">性别</label>
				<div class="col-sm-3">
					<select class="form-control" name="u_sex">
					  <option value="男">男</option>
					  <option value="女">女</option>
					</select>
				</div>
				<label  class="col-sm-1 control-label">手机号</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" name="u_phone" value="<?php echo $user['u_phone'] ?>">
				</div>
			  </div>
			  <div class="form-group">
				  <label  class="col-sm-3 control-label">简介</label>
			  		<div class="col-sm-7">
						<textarea class="form-control" rows="4" name="u_info"><?php echo $user['u_info'] ?></textarea>
					</div>
			  </div>
			  <div class="form-group">
			  		<label  class="col-sm-3 control-label">寄/收件地址</label>
			  		<div class="col-sm-7">
			  			<textarea class="form-control" rows="2" name="u_address"><?php echo $user['u_address'] ?></textarea>
			  		</div>
			  </div>
			  <div class="form-group">
				<div class="col-sm-offset-3 col-sm-3">
				  <button type="submit" class="btn btn-default">保存</button>
				</div>
			  </div>
			</form>	
		</div>

		<div class="bottom" id="post">
			<div class="nav"><span>动态</span></div>
			<div class="content">
				<div class="nav_item">
					<table class="table table-hover table-condensed">
						<thead>
						</thead>
						<tbody>
							<?php foreach($data as $value){?>
							<tr onclick="location.href='Forum_post_item.php?Id=<?php echo $value->{'Id'}?>'">
								<td><p><?php echo $value->{'post_title'}?></p></td>
								<td><?php echo $value->{'post_module_name'}?></td>
								<td></td>
								<td></td>
								<?php 
									$post_id=$value->{"Id"};
									$sql="select * from comment where post_id='$post_id' order by create_time desc limit 0, 1 ";
									$result=$mysql->exec($sql);
									
								?>
								
								<td>
									<p class="username"><?php echo isset($result['u_name'])?$result['u_name']:"-"?></p>
									<p><?php echo isset($result['create_time'])?$result['create_time']:"-"?><p>
								</td>
								
								<td><?php echo $value->{'post_time'}?></td>
								<!-- 已审核显示审核通过或未通过/未审核显示未审核 -->
								<?php if($value->{'audit'}==1) { ?>
								<td><?php if($value->{'audit_result'}==1) echo '审核通过';else if($value->{'audit_result'}==10) echo '禁止';else echo '审核未通过' ?></td>
								<?php }else{ ?>
								<td>未审核</td>
								<?php } ?>
								<!-- 审核未通过显示原因/其他不显示 -->
								<?php if($value->{'audit_result'}==0 ||$value->{'audit_result'}==10){ ?>
								<td class="red">
									<?php if($value->{'reason'}==1) echo '不适当内容';else if($value->{'reason'}==2) echo '存在侵权行为';else if($value->{'reason'}==3) echo '仿冒商品信息';else if($value->{'reason'}==4) echo '政治反动内容';else if($value->{'reason'}==5) echo "其他" ?>
								</td>
								<?php }else{ ?>
								<td></td>
								<?php } ?>
								<td><a href="####" onclick="remove(<?php echo $value->{'Id'}?>);stopDefault(event)">删贴</a></td>
							<?php }?>
							</tr>
						</tbody>
					</table>
					<div class="more more_0">
						<a href='####' onclick="go(<?php echo $sum+1?>)">
							<?php if($more==1)echo '显示更多';else echo '已全部加载';?>
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
			<p>&copy;2019 李升典 童观锐 .AllRightsReserved</p>
		</div>
	</div>
	
	
</body>
	
	<!-- 百度编辑器 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.config.js"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.all.js"></script>
	<!-- 语言类型 -->
	<script type="text/javascript" src="../assets/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!-- 实例化编辑器 -->
	<script type="text/javascript">
	
		//模仿数据缓冲
		/* 帖子 */
		function go(sum){
			$(".more_0 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?sum="+sum+"#post";
			},1000);
		}
		
		//删帖
		function remove(id){
			if(confirm("确定删除该帖子吗？")){
				$.ajax({	
					url:"Forum_user_service.php",
					type:"GET",
					data:{op:1,id:id},
					success: function(msg){
						if(msg==1){
							alert("删贴成功！");
							location.href="Forum_user.php";
						}else{
							alert("系统出错！");
						}
					},
					error:function(e){ 
						alert("前后端交互失败！"); 
					}
				})
			}
		}
		
		//帖子上点击删除键后阻止上层的页面跳转冒泡(阻止跳转页面)
		function stopDefault(e){
			e.stopPropagation();
		}
		//提交发布商品前检测个人资料地址填写
		function checkForm(){
			var text=$("#address").val();
			if(text!=''){
				return true;
			}else{
				alert("请完善地址后发布");
				return false;
			}
		}
		$(function(){
			/* 用户名 */
			$.ajax({
				url:"../index.php",
				type:"POST",
				data:{data:'user'},
				success: function(msg){
					if(msg==1){
						window.location.href="login.html";
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
			$(".left a").click(function(){
				$(".new_img").slideToggle(300);
			});

			$(".text button").eq(0).click(function(){
				$(".update").slideToggle(500);
			});
			$("table td:contains('审核通过')").addClass('green');
			$("table td:contains('审核未通过')").addClass('red');
			$("table td:contains('禁止')").addClass('red');
			$("table td:contains('未审核')").addClass('blue');
			$("ul li span:contains('审核未通过')").addClass('red');
			$("ul li span:contains('禁止')").addClass('red');
			$("ul li span:contains('未审核')").addClass('blue');
		});
	</script>
</html>