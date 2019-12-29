<?php
	header("content-type:text/html;charset=utf-8"); 
	$pach= $_SERVER['DOCUMENT_ROOT'];
	include $pach.'/Forum/utils/MYSQL.php';
	$mysql=new Mysql();
	
	//获取当前用户
	session_start();
	if(isset($_SESSION['u_id'])){
		$user_id=$_SESSION['u_id'];
	}else{
		header("Location:login.html");
	}
	
	$sql="select * from module where module_type=1";
	$data=$mysql->queryAll($sql);
	$data=json_decode($data);

?>
<!DOCTYPE html>
<html>
<head>
	<title>帖子发布</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/Forum_post_style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/all_style.css">
	
	
    <script src="../assets/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="../assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jq_ui.min.js"></script>
</head>
<body>
	<?php include "../header.html" ?><!-- 导入头部 -->

	<!-- 导航栏 -->
	<div id="nav" class="type_area">
		<ul class="nav_item">
			<li onclick="location.href='../index.html'">首页</li>
			<li onclick="location.href='innerCore.php'">贴吧模式</li>
			<li onclick="location.href='Forum_post.php'" style="background:#2d004e;">发帖</li>
			<li onclick="location.href='Forum_user.php'">个人中心</li>
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
				<li>贴子发布:</li>
			</ul>
		</div>
		<div class="content_body">
			<div class="col-sm-6" style="padding:0 2px 5px 0;">
				<select class="form-control" id="module_type">
				  <option value="1">专题区</option>
				  <option value="2">学习区</option>
				  <option value="3">服务区</option>
				</select>
			</div>
				<div class="col-sm-6" style="padding:0;">
					<select class="form-control" id="module">
					  <?php foreach($data as $value){?>
					  <option value="<?php echo $value->{'module_id'} ?>">
					  	<?php echo $value->{'module_name'}?>
					  	</option>
					  <?php } ?>
					</select>
				</div>	
				<input id="post_title"  name="post_title" type="text" class="form-control" placeholder="帖子标题...">
				<script id="post_comment" name="post_comment" type="text/plain" style="height: 300px;margin:5px 0 2px 0;"></script>
				<div class="cover">
					<span>上传帖子封面:</span><input id="post_cover_image" name="post_cover_image" type="file" title="上传封面">
				</div>
				<button id="sent" class="btn btn-primary btn-block" role="button">发布帖子</button>
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
	<!-- 百度编辑器 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.config.js"></script>
	<!-- 编辑器源码文件 -->
	<script type="text/javascript" src="../assets/ueditor/ueditor.all.js"></script>
	<!-- 语言类型 -->
	<script type="text/javascript" src="../assets/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!-- 实例化编辑器 -->
	<script type="text/javascript">
		var ue = UE.getEditor('post_comment', {
			toolbars: [
				['fontfamily','fontsize','bold','italic', 'underline','forecolor', 'indent','emotion','insertimage','undo','redo','cleardoc']
			],
			autoHeightEnabled: false
		});
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
			$("#module_type").change(function(){
				var module_type=$('#module_type').val();
				$.ajax({
					url:"Forum_post_service.php",
					type:"get",
					data:{"module_type":module_type},
					success:function(msg){
						//console.log(msg);
						var data=JSON.parse(msg);
						//console.log(data);
						//console.log(msg);
						/* 替换 */
						var html="";
						for(var i=0;i<data.length;i++){
							//html += "<option value='data[i]->{'module_id'}'>'data[i]->{'module_name'}'</option>";
							html+='<option value="'+data[i]['module_id']+'">'+data[i]['module_name']+'</option>';
						}
						$("#module").html(html);
					},
					error:function(e){
						alert("前后端交互失败！"); 
					}
				});
			});
			$("#sent").click(function(){
				var module_type=$('#module_type').val();
				var module_id=$('#module').val();
				var post_title=$("#post_title").val();
				var post_comment=ue.getContent();
				var post_cover_image = document.getElementById("post_cover_image").files[0];
				if(!(post_title==""||post_comment=="")){
					var formFile = new FormData();
					formFile.append("module_type",module_type);
					formFile.append("module_id",module_id);
					formFile.append("post_title",post_title);
					formFile.append("post_comment",post_comment);
					formFile.append("post_cover_image",post_cover_image); //加入文件对象
					$.ajax({
						url:"Forum_post_service.php",
						type:"post",
						cache: false,
						data: formFile,
						processData: false,
						contentType: false,
						success: function(msg){
							if(msg==0){
								alert("数据库出错！");
							}else if(msg==1){
								alert('发布成功，等待管理员审核。');
							}else if(msg==2){
								alert("请上传帖子封面！");
							}else if(msg==3){
								alert("识别内容出错，请勿使用太复杂的表情。");
							}else if(msg==4){
								alert("请上传正确格式的封面图片！")
							}else {
								alert(msg);
							}
						},
						error:function(e){
							alert("前后端交互失败！"); 
						}
					});
				}else if(post_title=="" && post_comment==""){
					alert("帖子标题和内容不允许为空！");
				}else if(post_title==""){
					alert("请正确填写帖子标题！");
				}else if(post_comment==""){
					alert("请正确填写帖子内容！");
				}
				
			});
		});
	</script>
</body>
</html>