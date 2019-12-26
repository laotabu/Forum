<?php
	header("content-type:text/html;charset=utf-8");
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	$sql="select * from user where u_type=2 order by u_create_time desc";
	$data=$mysql->queryAll($sql);
	$data = json_decode($data);
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
		<title>管理员界面-用户管理</title>
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
						<th>用户名</th>
						<th>邮箱</th>
						<th>手机号</th>
						<th>账号状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($data as $value){ ?>
					<tr>
						<td><?php echo $value->{'u_name'}?></td>
						<td><?php echo $value->{'u_email'}?></td>
						<td><?php echo $value->{'u_phone'}?></td>
						<td><?php if($value->{'is_enable'}==0)echo "锁定";else if($value->{'is_enable'}==1) echo "正常";else if($value->{'is_enable'}==2) echo "冻结"?></td>
						<td>
							<!-- 锁定/解锁/解冻 -->
							<a href="#" onclick="enable(<?php echo $value->{'u_id'} ?>,<?php echo $value->{'is_enable'}?>)">
								<?php if($value->{'is_enable'}==0||$value->{'is_enable'}==1)echo "账号冻结";else if($value->{'is_enable'}==2) echo "解除冻结";?>
							</a>
							


							<a href="#" onclick="remove(<?php echo $value->{'u_id'} ?>)">删除</a>
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
		
	</body>
	<script>
		$(function(){
			//导航选中特效
			$("#nav li:contains('用户管理')").addClass("li_on");
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

			$("table td:contains('正常')").addClass('green');
			$("table td:contains('冻结')").addClass('red');

		});
		function update(id){
			$("#id").val(id);
		}
		function remove(id){
			if(confirm("确定删除该用户吗？")){
				window.location.href="manage_module_core.php?op=3&u_id="+id;
			}
		}
		function enable(id,is_enable){
			window.location.href="manage_module_core.php?op=4&u_id="+id+"&is_enable="+is_enable;
		}
	</script>
</html>