<div id="divMain">
	<div class="divHeader" ><?php echo $zbp->title ?></div>
	<div class="SubMenu"><?php ZBlogSEO_SubMenu(6);?></div>
	<div class="SubMenu"> 
		<a href="?act=config" ><span class="m-left ">基本设置</span></a>
		<a href="?act=index" ><span class="m-left">爬行榜单</span></a>
		<a href="?act=today" ><span class="m-left m-now">今日到访</span></a>
		<a href="?act=list" ><span class="m-left ">所有记录</span></a>
	</div>
	<div id="divMain2">
	<div class="box_title"><?php echo $box_title .$box_title_button; ?></div>
		<table border="1" class="tableFull tableBorder tableBorder-thcenter">
			<tr >
				<th class="td20"> 蜘蛛名称 </th>
				<th class="td20"> 蜘蛛IP </th>
				<th class="td20"> 到访时间 </th>
				<th class=""> 抓取次数 </th>
			</tr>
			<?php foreach ($List as $data){?>
				<tr>
					<td> <a href="<?php echo $zbp->host .'zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name='.$data->Name.'&ip='.$_ip ?>"><?php echo $data->Name ?></a></td>
					<td> <a href="<?php echo $zbp->host .'zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name='.$_name.'&ip='.$data->IP ?>"><?php echo $data->IP ?></a></td>
					<td> <?php echo $data->Time() ?></td>
					<td> <?php echo $data->Nums ?></td>
				</tr>
			<?php }?>

		</table>
	</div>
</div>
<script type="text/javascript">
    AddHeaderIcon("<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/logo.png");
    ActiveTopMenu("topmenu_ZBlogSEO");
</script>