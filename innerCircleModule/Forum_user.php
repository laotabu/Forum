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

	/*$sql="select * from `post` where post_user_id=$u_id and audit_result=1 order by post_time desc";
	$select_data=$mysql->queryAll($sql);;//储存一个数据供发布商品的关联帖子使用
	$select_data = json_decode($select_data);*/
	
	
	/* //商品购买记录
	$sql="select * from `commodity` where `commodity_buy`=$u_id order by `create_time` desc";
	$buy_data=$mysql->queryAll($sql); */
	
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
	/*
	//展示商品
	$sql="select * from `commodity` where u_id=$u_id and `commodity_buy`=0  order by `create_time` desc";
	$product=$mysql->queryAll($sql);
	//数据缓存_商品
	$productLength=count($product);//数组长度
	$toLoad_sum_1=ceil($productLength/5);//最大加载次数
	$more_1=1;//more_1=1说明数据未缓存完
	$sum_1=isset($_GET['sum_1'])?$_GET['sum_1']:'1';//默认加载次数 1 次
	if($sum_1>$toLoad_sum_1)$sum_1=$toLoad_sum_1;//限制sum_1不超过最大加载
	$toLoad_1=5;//加载一次多显示5条内容
	$content_1=$sum_1*$toLoad_1;//共加载的内容条数
	$product=array_slice($product,0,$content_1);
	if($content_1>=$productLength){
		//more_1=0说明数据已缓存完
		$more_1=0;
	}
	//商品卖出记录
	$sql="select * from `commodity` where u_id=$u_id and `commodity_buy`!=0  order by `create_time` desc";
	$sale_data=$mysql->queryAll($sql);
	//数据缓存_商品卖出
	$sale_dataLength=count($sale_data);//数组长度
	$toLoad_sum_2=ceil($sale_dataLength/4);//最大加载次数
	$more_2=1;//more_2=1说明数据未缓存完
	$sum_2=isset($_GET['sum_2'])?$_GET['sum_2']:'1';//默认加载次数 1 次
	if($sum_2>$toLoad_sum_2)$sum_2=$toLoad_sum_2;//限制sum_2不超过最大加载
	$toLoad_2=4;//加载一次多显示5条内容
	$content_2=$sum_2*$toLoad_2;//共加载的内容条数
	$sale_data=array_slice($sale_data,0,$content_2);
	if($content_2>=$sale_dataLength){
		//more_2=0说明数据已缓存完
		$more_2=0;
	}
	//商品买入记录
	$sql="select * from `commodity` where  `commodity_buy`=$u_id  order by `create_time` desc";
	$buy_data=$mysql->queryAll($sql);
	//数据缓存_商品买入
	$buy_dataLength=count($buy_data);//数组长度
	$toLoad_sum_3=ceil($buy_dataLength/4);//最大加载次数
	$more_3=1;//more_3=1说明数据未缓存完
	$sum_3=isset($_GET['sum_3'])?$_GET['sum_3']:'1';//默认加载次数 1 次
	if($sum_3>$toLoad_sum_3)$sum_3=$toLoad_sum_3;//限制sum_3不超过最大加载
	$toLoad_3=4;//加载一次多显示5条内容
	$content_3=$sum_3*$toLoad_3;//共加载的内容条数
	$buy_data=array_slice($buy_data,0,$content_3);
	if($content_3>=$buy_dataLength){
		//more_3=0说明数据已缓存完
		$more_3=0;
	}
	*/
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
			<!--<li onclick="location.href='Forum_module.php?module_type=1&module_id=1'">帖子分类</li>!-->
			<li onclick="location.href='Forum_post.php'">发帖</li>
			<!--<li onclick="location.href='Forum_store.php'">商城</li>-->
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
							echo '../assets/img/defaultHead.jpg';
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
				<!--<button type="button" class="btn btn-default  btn-xs float_right"style="margin-right:5px;">
				  <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>&nbsp;编辑信息
				</button>
				-->
				
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
		<!-- 发布商品 -->
		<!--<div class="add" style="display: none;">
			<h4>发布商品</h4>
			<form class="form-horizontal" action="Forum_user_service.php" method="post" enctype="multipart/form-data"  onsubmit="return checkForm()">
				<input type="hidden"id="id" name="id"> 传递商品id
				<input type="hidden" name="op" value="3"> 区别请求内容
			  <div class="form-group">
				<label for="commodity_title" class="col-sm-3 control-label">商品标题</label>
				<div class="col-sm-7">
				  <input type="text" class="form-control" id="commodity_title" name="commodity_title" placeholder="商品标题" required>
				</div>
			  </div>
			  <div class="form-group">
			  	<label for="commodity_price" class="col-sm-3 control-label">商品价格</label>
			  	<div class="col-sm-7">
			  	  <input type="number" class="form-control" id="commodity_price" name="commodity_price" required>
			  	</div>
			  </div>
			  <div class="form-group" title="寄/收件地址,可在个人资料更改">
			  	<label for="address" class="col-sm-3 control-label">寄/收件地址</label>
				<div class="col-sm-7">
					<input type="text" class="form-control"  value="<?php echo $user['address'] ?>" disabled>
				</div>
			  </div>
			  <div class="form-group">
				<label for="pickup_way" class="col-sm-3 control-label">取货方式</label>
				<div class="col-sm-3">
					<select class="form-control" id="pickup_way" name="pickup_way">
					  <option value="1">上门自取</option>
					  <option value="2">送货上门</option>
					</select>
				</div>
				<label for="commodity_type" class="col-sm-1 control-label">类型</label>
				<div class="col-sm-3">
					<select class="form-control" id="commodity_type" name="commodity_type">
					  <option value="4">其他</option>
					  <option value="1">生活用品</option>
					  <option value="2">服饰鞋包</option>
					  <option value="3">电子设备</option>
					</select>
				</div>
			  </div>
			  <div class="form-group">
			  	<label for="commodity_cover_image" class="col-sm-3 control-label">上传封面</label>
			  	<div class="col-sm-7">
			  	  <input type="file" id="commodity_cover_image" name="commodity_cover_image" title="上传封面" required>
			  	</div>
			  </div>
			  <div class="form-group">
			  		<div class="col-sm-offset-2 col-sm-8">
						<script id="commodity_details" name="commodity_details" type="text/plain" style="height: 450px;">商品详情...	</script>
					</div>
			  </div>
			  <div class="form-group">
			  	<label for="post_id" class="col-sm-3 control-label">关联帖子</label>
			  	<div class="col-sm-3">
			  		<select class="form-control" id="post_id" name="post_id">
					  <option value="0">否</option>
					  <?php foreach($select_data as $select_value){ ?>
			  		  <option value="<?php echo $select_value['id']?>"><?php echo $select_value['post_title']?></option>
					  <?php } ?>
			  		</select>
			  	</div>
			  </div>
			  <div class="form-group">
				<div class="col-sm-offset-2 col-sm-8">
				  <input type="hidden" id="address" value="<?php echo $user['address']?>">
				  <button type="submit" class="btn btn-primary btn-block">确认发布</button>
				</div>
			  </div>
			</form>	
		</div>


-->


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
		<!--
		<div class="bottom" id="show">
			<div class="nav"><span>发布的商品</span></div>
			<div class="content">
				<div class="nav_item">
					<ul>
						<?php foreach($product as $value){?>
						<li>
							<a href="Forum_product_info.php?id=<?php echo $value['id']?>">
								<img src="<?php echo $value['commodity_cover_image'] ?>" alt="">
							</a>
							<p  onclick="location.href='Forum_product_info.php?id=<?php echo $value['id']?>'"><?php echo $value['commodity_title'] ?></p>
							<span>￥<?php echo $value['commodity_price'] ?></span>
							<span><a href="Forum_product_info.php?id=<?php echo $value['id']?>"><?php echo date('Y-m-d',strtotime($value['create_time'])); ?> </a></span>
							<span><?php if($value['audit']==0 )echo '审核未通过';else if($value['audit']==2)echo '未审核';else if($value['audit']==10)echo '禁止' ?></span>
							<span>
								<a href="####" onclick="update(<?php echo $value['id'] ?>)" data-toggle="modal" data-target="#myModal">编辑</a>
								<a href="####" onclick="remove_1(<?php echo $value['id'] ?>)">下架</a>
							</span>
						</li>
						<?php } ?>
					</ul>
					<div class="more more_1">
						<a href='####' onclick="go_1(<?php echo $sum_1+1?>)">
							<?php if($more_1==1)echo '显示更多';else echo '已全部加载';?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom" id="sale">
			<div class="nav"><span>商品卖出记录</span></div>
			<div class="content">
				<div class="nav_item">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>商品类型</th>
								<th>商品标题</th>
								<th>商品价格</th>
								<th>买家</th>
								<th>手机号</th>
								<th>寄件地址</th>
								<th>购买日期</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($sale_data as $value){ 
								//获取购买人的信息
								$buy_id=$value['commodity_buy'];
								$sql="select `username`,`phone`,`address` from `user` where id = $buy_id";
								$buy_user=$mysql->exec($sql);	
							?>
							<tr>
								<td>
									<?php if($value['commodity_type']==1)echo '生活用品';else if($value['commodity_type']==2)echo '服饰鞋包';else if($value['commodity_type']==3)echo '电子设备';else if($value['commodity_type']==4)echo '其他'; ?>
								</td>
								<td><p><?php echo $value['commodity_title']?></p></td>
								<td><?php echo $value['commodity_price']?> 元</td>
								<td><?php echo $buy_user['username'] ?></td>
								<td><?php echo $buy_user['phone'] ?></td>
								<td><p><?php if($value['pickup_way']==2)echo $buy_user['address'];else echo '买家自取'?></p></td>
								<td><p><?php echo $value['create_time'] ?></p></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="more more_2">
						<a href='####' onclick="go_2(<?php echo $sum_2+1?>)">
							<?php if($more_2==1)echo '显示更多';else echo '已全部加载';?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom" id="buy">
			<div class="nav"><span>商品购买记录</span></div>
			<div class="content">
				<div class="nav_item">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>商品类型</th>
								<th>商品标题</th>
								<th>商品价格</th>
								<th>卖家</th>
								<th>手机号</th>
								<th>取件地址</th>
								<th>购买日期</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($buy_data as $value){ 
								//获取卖家的信息
								$sale_id=$value['u_id'];
								$sql="select `username`,`phone`,`address` from `user` where id = $sale_id";
								$sale_user=$mysql->exec($sql);
							?>
							<tr>
								<td>
									<?php if($value['commodity_type']==1)echo '生活用品';else if($value['commodity_type']==2)echo '服饰鞋包';else if($value['commodity_type']==3)echo '电子设备';else if($value['commodity_type']==4)echo '其他'; ?>
								</td>
								<td><p><?php echo $value['commodity_title']?></p></td>
								<td><?php echo $value['commodity_price']?> 元</td>
								<td><?php echo $sale_user['username'] ?></td>
								<td><?php echo $sale_user['phone'] ?></td>
								<td><p><?php if($value['pickup_way']==1)echo $sale_user['address'];else echo '送货上门'?></p></td>
								<td><?php echo $value['create_time'] ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="more more_3">
						<a href='####' onclick="go_3(<?php echo $sum_3+1?>)">
							<?php if($more_3==1)echo '显示更多';else echo '已全部加载';?>
						</a>
					</div>
				</div>
			</div>
		</div>
	-->
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
	<!-- Modal -->
	<!--
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">编辑模块简介：</h4>
	      </div>
	      <div class="modal-body" style="text-align: center;">
	      	模态框内容
	      		<form id="form" action="Forum_user_service.php" method="post" enctype="multipart/form-data">
					<input type="hidden" id="id_1" name="id">
					<input type="hidden" name="op" value="5">区别请求内容
					 
					<table style="margin: 0 auto;">
						<tr>
							<td>商品标题：</td>
							<td><input type="text" class="form-control" name="commodity_title" id="commodity_title_1"></td>
						</tr>
						<tr>
							<td>商品价格：</td>
							<td><input type="text" class="form-control" name="commodity_price" id="commodity_price_1"></td>
						</tr>
						<tr>
							<td title="寄/收件地址,可在个人资料更改">寄 / 收件：</td>
							<td title="寄/收件地址,可在个人资料更改"><input type="text" class="form-control" value="<?php echo $user['address'] ?>" disabled></td>
						</tr>
						<tr>
							<td>取货方式：</td>
							<td>
								<select class="form-control" name="pickup_way">
								  <option value="1">上门自取</option>
								  <option value="2">送货上门</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>商品类型：</td>
							<td>
								<select class="form-control" name="commodity_type">
								  <option value="4">其他</option>
								  <option value="1">生活用品</option>
								  <option value="2">服饰鞋包</option>
								  <option value="3">电子设备</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>关联帖子：</td>
							<td>
								<select name="post_id" class="form-control">
								  <option value="0">否</option>
								  <?php foreach($select_data as $select_value){ ?>
								  <option value="<?php echo $select_value['id']?>"><?php echo $select_value['post_title']?></option>
								  <?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>修改封面：</td>
							<td><input type="file" name="commodity_cover_image" title="封面"></td>
						</tr>
						<tr>
							<td colspan="2"><script id="commodity_details_1" name="commodity_details" type="text/plain" style="height: 450px;width: 100%;text-align: left;">商品详情...	</script></td>
						</tr>
					</table>
				</form>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-primary" form="form">保存</button>提交按钮 链接到form表单
	        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
	      </div>
	    </div>
	  </div>
	</div>

-->
</body>
	
	<!-- 百度编辑器 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.config.js"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.all.js"></script>
	<!-- 语言类型 -->
	<script type="text/javascript" src="../assets/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!-- 实例化编辑器 -->
	<script type="text/javascript">
		/*var ue = UE.getEditor('commodity_details', {
			toolbars: [
				['fontfamily','fontsize','bold','italic', 'underline','forecolor', 'indent','emotion','insertimage','undo','redo','cleardoc']
			],
			autoHeightEnabled: false
		});
		var ue_2 = UE.getEditor('commodity_details_1', {
			toolbars: [
				['fontfamily','fontsize','bold','italic', 'underline','forecolor', 'indent','emotion','insertimage','undo','redo','cleardoc']
			],
			autoHeightEnabled: false
		});*/
		//模仿数据缓冲
		/* 帖子 */
		function go(sum){
			$(".more_0 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?sum="+sum+"#post";
			},1000);
		}
		/* 商品 */
		/*function go_1(sum){
			$(".more_1 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?sum_1="+sum+"#show";
			},1000);
		}*/
		/* 商品卖出 */
		/*function go_2(sum){
			$(".more_2 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?sum_2="+sum+"#sale";
			},1000);
		}*/
		/*function go_3(sum){
			$(".more_3 a").text("加载中...");
			setTimeout(function(){
				window.location.href="?sum_3="+sum+"#buy";
			},1000);
		}*/
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
		//删除帖子
		/*function remove_1(id){
			if(confirm("确定删除该商品吗？")){
				$.ajax({	
					url:"Forum_user_service.php",
					type:"GET",
					data:{op:2,id:id},
					success: function(msg){
						if(msg==1){
							alert("商品下架成功！");
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
		}*/
		//编辑
		/*function update(id){
			$("#id_1").val(id);
			$.ajax({
				url:"Forum_user_service.php",
				type:"POST",
				data:{op:'4',id:id},
				success: function(msg){
					data=JSON.parse(msg);
					$("#commodity_title_1").val(data['commodity_title']);
					$("#commodity_price_1").val(data['commodity_price']);
				},
			    error:function(e){
			    	alert("前后端交互失败！"); 
			    }
			})
		}*/
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
						$("#u_name").text(data['u_name']);
						$(".user_item li img").attr('src',data['u_image']);
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
			/*$(".text button").eq(1).click(function(){
				$(".add").slideToggle(800);
			});*/
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