<style>
.zeshis{
    float: left;
    width: 85%;
    word-break: keep-all;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.youcehs{
    float: left;
    width: 15%;
    text-align: center;
    font-size: 13px;
}
td{}
.sasaa{width: 80%;}
.sasaa a{
	width: 100%;
    float: left;
    height: 30px;
    overflow: hidden;
    line-height: 30px;
	word-break: break-all;
}
</style>
<div id="divMain">
	<div class="divHeader" ><?php echo $zbp->title ?></div>
	<div class="SubMenu"><?php ZBlogSEO_SubMenu(6);?></div>
	<div class="SubMenu"> 
		<a href="?act=config" ><span class="m-left ">基本设置</span></a>
		<a href="?act=index" ><span class="m-left m-now">爬行榜单</span></a>
		<a href="?act=today" ><span class="m-left ">今日到访</span></a>
		<a href="?act=list" ><span class="m-left ">所有记录</span></a>
	   
	</div>
	<div id="divMain2">
		<div style="width: 100%;overflow: hidden;">
			<table style="width: 25%;float: left;" border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tbody>
					<tr>
						<th>爬行榜单</th>
						<th style="width: 20%;"><a href="?act=ListByURL" class="button" target="_blank">更多</a> </th>
					</tr>
					<?php foreach ($ListByURL as $data){?>
						<tr>
							<td class="sasaa"><a  title="<?php echo $data->Url;?>"><?php echo $data->Url;?></a></td>
							<td align="center" style="width: 20%;"><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&url=<?php echo urlencode($data->Url) ?>" title="详细记录"  class="button"><?php echo $data->Nums ?></a></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
			<table style="width: 25%;float: left;" border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tbody>
					<tr>
						<th>来访排行</th>
						<th class="td20"><a href="?act=ListByName" class="button" target="_blank">更多</a></th>
					</tr>
					
					<?php foreach ($ListByName as $data){ ?>
					<tr>
							<td><?php echo $data->Name;?></td>
							<td align="center"><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name=<?php echo $data->Name?>" title="详细记录"  class="button"><?php echo $data->Nums?></a></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<table style="width: 25%;float: left;" border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tbody>
					<tr>
						<th>错误记录</th>
						<th class="td20"><a href="?act=ListByERR" class="button" target="_blank">更多</a></th>
					</tr>
					<?php foreach ($ListByERR as $data){ ?>
						<tr>
								<td class="sasaa"><a  title="<?php echo $data->Url;?>">【<?php echo $data->Status;?>】<?php echo $data->Url;?></a></td>
								<td align="center" style="width: 20%;"><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=errlist&url=<?php echo urlencode($data->Url) ?>" title="详细记录"  class="button"><?php echo $data->Nums?></a></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
			<table style="width: 25%;float: left;" border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tbody>
					<tr>
						<th>最近来访</th>
						<th class="td20"></th>
					</tr>
					<?php foreach ($ListLast as $data){?>
						<tr>
								<td class="sasaa"><?php echo $data->Time();?></td>
								<td align="center" style="width: 20%;"><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name=<?php echo $data->Name ?>" title="详细记录"  class="button"><?php echo $data->Name?></a></td>
						</tr>
						
					<?php }?>
				</tbody>
			</table>
		</div>
		<div class="divMain3">
			<table border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tbody>
				<tr >
					<th> ID </th>
					<th> 蜘蛛名称 </th>
					<th> 蜘蛛IP </th>
					<th> 抓取时间 </th>
					<th> 抓取地址 </th>
					<th> 抓取状态 </th>
				</tr>
				<?php foreach ($ListData['data'] as $data){?>
					<tr  class="td5">
						<td> <?php echo $data->ID ?></td>
						<td> <?php echo $data->Name ?></td>
						<td> <?php echo $data->IP ?></td>
						<td> <?php echo $data->Time() ?></td>
						<td> <?php echo $data->url_txt ?></td>
						<td> <?php echo $data->Status ?></td>
					</tr>
				<?php }?>
				</tbody>
			</table>
			<p class="pagebar">
				<?php foreach ($ListData['pagebar']->buttons as $key => $value){?>
					<a href="<?php echo $value ?>"> <?php  echo $key ?></a>&nbsp;&nbsp;
				<?php }?>
			</p>
		</div>
	</div>
	</div>
<script type="text/javascript">
    AddHeaderIcon("<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/logo.png");
    ActiveTopMenu("topmenu_ZBlogSEO");
</script>