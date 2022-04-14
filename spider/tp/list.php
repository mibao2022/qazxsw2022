<div id="divMain">
	<div class="divHeader" ><?php echo $zbp->title ?></div>
	<div class="SubMenu"><?php ZBlogSEO_SubMenu(6);?></div>
	<div class="SubMenu"> <a href="?act=config" ><span class="m-left ">基本设置</span></a>
		<a href="?act=index" ><span class="m-left">爬行榜单</span></a>
		<a href="?act=today" ><span class="m-left ">今日到访</span></a>
		<a href="?act=list" ><span class="m-left m-now">所有记录</span></a>
	</div>
<div id="divMain2">
    <div class="divMain3">
        <table border="1" class="tableFull tableBorder tableBorder-thcenter">
            <tr >
                <th class="td5"> ID </th>
                <th class="td10"> 蜘蛛名称 </th>
                <th class="td5"> 蜘蛛IP </th>
                <th class="td20"> 抓取时间 </th>
                <th class=""> 抓取地址 </th>
                <th class="td5"> 状态 </th>
            </tr>
            <?php foreach ($ListData['data'] as $data){?>
                <tr>
                    <td> <?php echo $data->ID ?></td>
                    <td> <a href="<?php echo $zbp->host .'zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name='.$data->Name.'&ip='.$data->IP ?>"><?php echo $data->Name ?></a></td>
                    <td> <a href="<?php echo $zbp->host .'zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name='.$data->Name.'&ip='.$data->IP ?>"><?php echo $data->IP ?></a></td>
                    <td> <?php echo $data->Time() ?></td>
                    <td> <a href="<?php echo $data->Url;?>" title="<?php echo $data->Url;?>"><?php echo $data->url_txt ?></a></td>
                    <td> <?php echo $data->Status ?></td>
                </tr>
            <?php }?>

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