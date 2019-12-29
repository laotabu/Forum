<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	// include $pach.'/Forum/MYSQL.php';
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	$sql="select * from `post` where `audit_result`=10 order by post_time desc";
	$data= json_decode($mysql->queryAll($sql));
	/* 模糊查询 */
	if(isset($_GET['select'])){
		$text=$_GET['select'];
		$sql="SELECT * FROM `post` where `post_title` Like '%$text%' and `audit_result`=10  order by post_time desc";
		$data= json_decode($mysql->queryAll($sql));
	}
	/* 分页 */
	/* $data=array_slice($data,0,6); 取数组第0个元素（包括第0）往后6个元素*/
	$nowPage=1;	//初始化当前页数为1
	$pageNum=10;//页面显示条数
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
	$data=array_slice($data,$pageStart,10);
	
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
	//初始化一个$id
	$id='';
?>
<html>
	<header>
		<meta charset="utf-8">
		<title>管理员界面-禁止记录</title>
		<link rel="stylesheet" href="../assets/css/all.css">
		<link rel="stylesheet" href="../assets/css/module_style.css">
		<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
		<script type="text/javascript" src="../assets/js/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.js"></script>
	</header>
	<body>

		<!-- <?php //include './header.html' ?> -->
		<?php include 'manageWholePage.html' ?>
		<!-- 搜索 -->
		<div id="select">
			<div class="col-md-8 col-sm-0 col-xs-0"></div>
			<form action="" method="get">
			<div class="input-group col-md-4 col-sm-4 col-xs-4">
				<input type="text" class="form-control" style="position:static" placeholder="搜索..." name="select">
				<span class="input-group-btn">
				 	<button class="btn btn-default" type="submit">查询</button>
				</span>
			</div>
			</form>
		</div>
		<div id="content">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>模块名字</th>
						<th>帖子标题</th>
						<th>发表用户</th>
						<th>发布时间</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($data as $value){ ?>
					<tr>
						<td><?php echo $value->post_module_name ?></td>
						<td><p><?php echo $value->post_title ?></p></td>
						<td><?php echo $value->post_user_name ?></td>
						<td><?php echo $value->post_time ?></td>
						<td>
							<a href="###" onclick="look(<?php echo $value->Id;  ?>)" data-toggle="modal" data-target="#myModal">查看详情</a>
							<a href="###" onclick="update(<?php echo $value->Id;  ?>)">取消禁止</a>
						</td>
					</tr>
					<?php } ?>
					
				</tbody>
			</table>
			<!--分页按钮-->
			<nav aria-label="Page navigation" class="page">
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
		
		<!-- Modal 1 -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document" >
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">查看帖子详情：</h4>
		      </div>
		      <div class="modal-body">
		      	<!--模态框内容-->
		      		<!-- 获取点击的帖子内容 -->
		      		<h4 id="post_title"></h4>
		      		<p id="post_module_type"></p>
		      		<p id="post_module_name"></p>
		      		<p id="post_comment"></p>
		      </div>
		      <div class="modal-footer">
				  <button type="button" id="remove" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#myModal_2">取消禁止</button>
		          <button type="button" class="btn btn-primary" data-dismiss="modal">取消</button>
		      </div>
		    </div>
		  </div>
		</div>
		
	</body>
	<script>
		$(function(){
			//导航选中特效
			$("#nav li:contains('帖子管理')").addClass("li_on");
			$("#nav .ul1 .post").text("禁止记录");
			$("#nav .ul2 li:contains('禁止记录')").css("background","#1d1d82");
			$.ajax({
				url:"../index.php",
				type:"POST",
				data:{data:'public_user'},
				success: function(msg){
					if(msg==1){
						window.location.href="../login.html";
					}
				}
			})
			$("#content button").click(function(){
				$(".add").fadeToggle(500);
			});
		});
		//取消禁止
		function update(id){
			if(confirm("允许该帖子发布？")){
				$.ajax({
					url:"manage_module_core.php",
					type:"POST",
					data:{id:id,op:5},
					success: function(msg){
						if(msg==1){
							alert("操作成功！");
							location.href="manage_postBan.php";
						}else{
							alert("数据库出错！")
						}
					}
				})
			}
		}
		//帖子查看详情
		function look(id){
			$.ajax({
				url:"manage_module_core.php",
				type:"POST",
				data:{id:id,op:3},
				success: function(msg){
					var data=JSON.parse(msg);
					$("#post_title").html(data['post_title']);
					if(data['post_module_type'] ==1){
						$("#post_module_type").text('_专题区_');
					}else if(data['post_module_type'] ==2){
						$("#post_module_type").text('_学习区_');
					}else if(data['post_module_type']==3){
						$("#post_module_type").text('_服务区_');
					}
					$("#post_module_name").text("所属模块："+data['post_module_name']);
					$("#post_comment").html(data['post_comment']);
					
					$("#id").val(id);
					$("#remove").attr("onclick","update("+id+")");//动态设置模态框按钮的形参值

				}
			})
		}
	</script>
</html>