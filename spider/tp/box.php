<div id="divMain">
	<div class="divHeader" ><?php /* EL PSY CONGROO */ echo $zbp->title ?></div>
	<div class="SubMenu"><?php ZBlogSEO_SubMenu(6);?></div>
	<div class="SubMenu"> <a href="?act=config" ><span class="m-left ">基本设置</span></a>
		<a href="?act=index" ><span class="m-left m-now">爬行榜单</span></a>
		<a href="?act=today" ><span class="m-left ">今日到访</span></a>
		<a href="?act=list" ><span class="m-left ">所有记录</span></a>
	</div>
	<div id="divMain2">
		<table border="1" class="tableFull tableBorder tableBorder-thcenter">
			<tr >
				<th class="td20"><?php echo $box_title ?></th>
				<th class="td20">详细记录</th>
			 </tr>
			<?php if($box_title=='来访排行'){foreach ($List as $data){?>
			    <tr>
                <td><?php echo $data->Name;?></td>
                <td><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&name=<?php echo $data->Name?>" title="详细记录"  class="button"><?php echo $data->Nums?></a></td>
				 </tr>
            <?php }
            }else{      	 	 	      		  		 
                    foreach ($List as $data){?>
					 <tr>
                        <td><a href="<?php echo $data->Url ?>" title="<?php echo $data->Url;?>"><?php echo $data->Url;?></a></td>
                        <td><a href="<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&url=<?php echo urlencode($data->Url) ?>" title="详细记录"  class="button"><?php echo $data->Nums ?></a></td>
						 </tr>
                    <?php }
                }?>
		</table>
	</div>
</div>
<script type="text/javascript">
    AddHeaderIcon("<?php echo $zbp->host ?>zb_users/plugin/ZBlogSEO/logo.png");
    ActiveTopMenu("topmenu_ZBlogSEO");
</script>