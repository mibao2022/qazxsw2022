<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="Cache-Control" content="no-transform"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>对不起，页面未找到</title>
    <link rel="stylesheet" rev="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/404/css/404.css" type="text/css" media="all"/>
</head>
<body>
<div class="wrapper-page">
    <div class="page-ex">
        <h1>404!</h1>
        <h2>对不起，页面未找到</h2><br>
        <p>找不到内容？尝试下我们的搜索吧!</p>
        <form name="search" method="post" action="<?php echo $zbp->host ?>zb_system/cmd.php?act=search">
		    <input type="text" name="q" size="11"> 
			<input type="submit" value="搜索">
		</form>
        <br>
        <a class="page-back" href="<?php echo $zbp->host ?>"><i class="fa fa-angle-left"></i> 返回首页</a>
    </div>
</div>
</body>
</html>