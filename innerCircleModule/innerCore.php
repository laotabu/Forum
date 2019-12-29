<?php
	header("content-type:text/html;charset=utf-8"); 
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	//页面刷新后的数据展示 /默认：推荐 post_hot>/
	$sql="select * from post where audit_result=1 order by post_hot desc";
	$data=$mysql->queryAll($sql);
	$data = json_decode($data);
	$type="0";
	//点击按钮后的数据筛选 type=1 :热门 2:新帖 3:最近回复 4:我的帖子
	if(!empty($_GET)){
		$type=isset($_GET['type'])?$_GET['type']:'';
		if($type==1){
			$sql="select * from post where audit_result=1  order by post_sum desc";
			$data=$mysql->queryAll($sql);
			$data = json_decode($data);
		}else if($type==2){
			$sql="select * from post where audit_result=1  order by post_time desc";
			$data=$mysql->queryAll($sql);
			$data = json_decode($data);
		}/* else if($type==3){
			$sql="select * from `post` order by post_sum desc";
			$data=$mysql->queryAll($sql);
		} */else if($type==4){
			//获取当前用户
			session_start();
			$post_user_id=$_SESSION['u_id'];
			
			$sql="select * from post where post_user_id='$post_user_id' and audit_result=1  order by post_time desc";
			$data=$mysql->queryAll($sql);
			$data = json_decode($data);
		}else if($type==5){ //模糊查询
			$text=isset($_GET['text'])?$_GET['text']:'';
			$sql="SELECT * FROM post where post_title Like '%$text%' and audit_result=1";
			$data=$mysql->queryAll($sql,"search");
			$data = json_decode($data);
		}
	}
	/* 分页 */
	/* $data=array_slice($data,0,6); 取数组第0个元素（包括第0）往后6个元素*/
	$nowPage=1;	//初始化当前页数为1
	$pageNum=15;//页面显示条数
	$total=count($data);//总条数
	$pageTotal=ceil($total/$pageNum);//总页数
	//使用a监听页面的改变
	if(isset($_GET['page'])){
		$nowPage=isset($_GET['page'])?$_GET['page']:1;
		//传入a值超出范围处理

		if($nowPage<1){
			$nowPage=1;
		}else if($nowPage>$pageTotal){
			$nowPage=$pageTotal;
		}
	}
	$pageStart=$nowPage*$pageNum-$pageNum;//每页的开始下标
	
	$pageLast=$nowPage-1;//上一页
	if($pageLast<1) $pageLast=1;
	
	$pageNext=$nowPage+1;//下一页
	if($pageNext>$pageTotal){
		$pageNext=$pageTotal;
	};
	//数据分割输出
	$data=array_slice($data,$pageStart,15);

	//分页按钮
	$html="";
	if($pageTotal<5){
		$i=1;
		$j=$pageTotal;
		for($i;$i<=$j;$i++){
			if($i==$nowPage){
				$html.="<li class='active'><a href='?page=".$i."'>".$i."</a><li>";
			}else{
				$html.="<li><a href='?page=".$i."'>".$i."</a><li>";
			}
		}
	}else if($pageTotal>$nowPage+4){
		$i=$nowPage;
		$j=$nowPage+4;
		for($i;$i<=$j;$i++){
			if($i==$nowPage){
				$html.="<li class='active'><a href='?page=".$i."'>".$i."</a><li>";
			}else{
				$html.="<li><a href='?page=".$i."'>".$i."</a><li>";
			}
		}
	}else{
		$i=$pageTotal-4;
		$j=$pageTotal;
		for($i;$i<=$j;$i++){
			if($i==$nowPage){
				$html.="<li class='active'><a href='?page=".$i."'>".$i."</a><li>";
			}else{
				$html.="<li><a href='?page=".$i."'>".$i."</a><li>";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>贴吧模式</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/all_style.css">
	<link rel="stylesheet" type="text/css" href="../assets/css/Forum_2_style.css"/>
    <script src="../assets/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jq_ui.min.js"></script>
	<!-- 模板引擎 -->
	<script type="text/javascript" src="../assets/js/template-web.js"></script>
</head>
<body>
	<?php include "../header.html" ?>

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
	<!-- content模块 -->
	<div id="content" class="type_area">
		<div class="post_nav">
			<ul>
				<!--<li onclick="select(0)"<?php if($type==0)echo 'class="pitchOn"'?>>推荐</li>-->
				<li onclick="select(1)"<?php if($type==1)echo 'class="pitchOn"'?>>最新热门</li>
				<li onclick="select(2)"<?php if($type==2)echo 'class="pitchOn"'?>>最新发帖</li>
				<!-- <li onclick="select(3)"<?php if($type==3)echo 'class="pitchOn"'?>>最新回复</li> -->
				<li onclick="select(4)"<?php if($type==4)echo 'class="pitchOn"'?>>我的帖子</li>
			</ul>
		</div>
		<div class="nav_item">
			<table class="table table-hover table-condensed">
				<thead>
					<tr>
						<th>标题</th>
						<th>帖子板块</th>
						<th>作者</th>
						<th>热度</th>
						<th>最近回复</th>
						<th>发表时间</th>
					</tr>
				</thead>
				<tbody>

					<?php 

					foreach($data as $value){?>
					<tr onclick="location.href='Forum_post_item.php?Id=<?php echo $value->{'Id'}?>'">
						<td><?php echo $value->{'post_title'}?></td>
						<td><?php echo $value->{'post_module_name'}?></td>
						<td><?php echo $value->{'post_user_name'}?></td>
						<td><?php echo $value->{'post_sum'}?></td>
						<?php 
							$post_id=$value->{"Id"};
							$sql="select * from comment where post_id='$post_id' order by comment_time desc limit 0, 1 ";
							$result=$mysql->exec($sql);

						?>	
						<td>
							<p class="username"><?php echo isset($result['comment_user_name'])?$result['comment_user_name']:"-"?></p>
							<p><?php echo isset($result['comment_time'])?$result['comment_time']:"-"?><p>
						</td>
						
						<td><?php echo $value->{'post_time'}?></td>
					<?php }?>
					</tr>
				</tbody>
			</table>
			<!--分页按钮-->
			<nav aria-label="Page navigation" class="float_right">
				<ul class="pagination">
					<li>
					    <a href="?page=<?php echo $pageLast?>" aria-label="Previous">
					        <span aria-hidden="true">&laquo;</span>
					    </a>
					</li>
					
					<?php echo $html ?>
					<li>
					    <a href="?page=<?php echo $pageNext?>" aria-label="Next">
					     	<span aria-hidden="true">&raquo;</span>
					    </a>
					</li>
			 	 </ul>
			</nav>
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
	

	<script>

		$(function(){
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
		function select(e){
				if(e==0) location.href="innerCore.php";
				else location.href="innerCore.php?type="+e;
			}
	</script>
</body>
</html>