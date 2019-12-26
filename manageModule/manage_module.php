<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	$sql="select * from module";
	$data=$mysql->queryAll($sql);
	$data=json_decode($data);
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
?>
<html>
	<header>
		<meta charset="utf-8">
		<title>管理员界面-模块管理</title>
		<link rel="stylesheet" href="../assets/css/all.css">
		<link rel="stylesheet" href="../assets/css/module_style.css">
		<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
		<script type="text/javascript" src="../assets/js/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.js"></script>


	</header>
	<body>
		<?php include './manageWholePage.html' ?>
		<div id="content">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>模块类型</th>
						<th>模块名字</th>
						<th>发布帖子数</th>
						<th>简介</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($data as $value){ ?>
					<tr>
						<td><?php
							if($value->{'module_type'}==1)echo '专题区';
							else if($value->{'module_type'}==2) echo '学习区';
							else if($value->{'module_type'}==3) echo '服务区';
						?></td>
						<td><?php echo $value->{'module_name'}?></td>
						<td><?php echo $value->{'post_sum'}?></td>
						<td><?php echo $value->{'module_explain'}?></td>
						<td>
							<a href="#" onclick="update(<?php echo $value->{'module_id'}?>)" data-toggle="modal" data-target="#myModal">编辑</a>
							<a href="#" onclick="remove(<?php echo $value->{'module_id'} ?>)">删除</a>
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
			<button class="btn btn-default">添加模块</button>
			<!-- 添加模块 -->
			<div class="add" style="display: none;">
				<form class="form-horizontal" action="manage_module_core.php" method="post">
					<input type="hidden" name="op" value="2">
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-1 control-label">模块类</label>
						<div class="col-sm-5">
							<select class="form-control" name="module_type">
							  <option value="1">专题区</option>
							  <option value="2">学习区</option>
							  <option value="3">服务区</option>
							</select>
						</div>
						<label for="inputEmail3" class="col-sm-1 control-label">模块名</label>
						<div class="col-sm-5">
						  <input type="text" class="form-control" id="inputEmail3" placeholder="Module name" name="module_name">
						</div>
				    </div>			
				  <div class="form-group">
					  <label for="inputPassword3" class="col-sm-1 control-label">简介</label>
				  		<div class="col-sm-11">
							<textarea class="form-control" rows="4" name="module_explain"></textarea>
						</div>
				  </div>
				  <div class="form-group">
					<div class="col-sm-offset-11 col-sm-1">
					  <button type="submit" class="btn btn-default">保存</button>
					</div>
				  </div>
				</form>		
			</div>
		</div>
		
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">编辑模块简介：</h4>
		      </div>
		      <div class="modal-body" style="text-align: center;">
		      	<!--模态框内容-->
		      		<form id="form" action="manage_module_core.php" method="post">
						<input type="hidden" id="id" name="id" >
						<input type="hidden" name="op" value="1"><!-- 区分访问数据 -->
						<textarea style="border:0;border-radius:5px;width: 500px;height: 100px;padding: 10px;resize: none;" placeholder="新闻内容编辑" name="module_explain"></textarea>		
					</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
		        <button type="submit" class="btn btn-primary" form="form">保存</button><!--提交按钮 链接到form表单-->
		      </div>
		    </div>
		  </div>
		</div>
		
	</body>
	<script>
		$(function(){
			//导航选中特效
			$("#nav li:contains('模块管理')").addClass("li_on");
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
		function update(id){
			$("#id").val(id);
		}
		function remove(id){
			if(confirm("确定删除该模块吗？")){
				window.location.href="manage_module_core.php?op=1&module_id="+id;
			}
		}
	</script>
</html>