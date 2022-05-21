<?php exit; ?>
</div>
<!--{if empty($topic) || ($topic[usefooter])}-->
<!--{eval $focusid = getfocus_rand($_G[basescript]);}-->
<!--{if $focusid !== null}-->
<!--{eval $focus = $_G['cache']['focus']['data'][$focusid];}-->
<!--{eval $focusnum = count($_G['setting']['focus'][$_G[basescript]]);}-->

<div class="focus" id="sitefocus">
  <div class="bm">
    <div class="bm_h cl"> <a href="javascript:;" onclick="setcookie('nofocus_$_G[basescript]', 1, $_G['cache']['focus']['cookie']*3600);$('sitefocus').style.display='none'" class="y" title="{lang close}">{lang close}</a>
      <h2> 
        <!--{if $_G['cache']['focus']['title']}-->{$_G['cache']['focus']['title']}<!--{else}-->{lang focus_hottopics}<!--{/if}--> 
        <span id="focus_ctrl" class="fctrl"><img src="{IMGDIR}/pic_nv_prev.gif" alt="{lang footer_previous}" title="{lang footer_previous}" id="focusprev" class="cur1" onclick="showfocus('prev');" /> <em><span id="focuscur"></span>/$focusnum</em> <img src="{IMGDIR}/pic_nv_next.gif" alt="{lang footer_next}" title="{lang footer_next}" id="focusnext" class="cur1" onclick="showfocus('next')" /></span> </h2>
    </div>
    <div class="bm_c" id="focus_con"> </div>
  </div>
</div>
<!--{eval $focusi = 0;}--> 
<!--{loop $_G['setting']['focus'][$_G[basescript]] $id}-->
<div class="bm_c" style="display: none" id="focus_$focusi">
  <dl class="xld cl bbda">
    <dt><a href="{$_G['cache']['focus']['data'][$id]['url']}" class="xi2" target="_blank">$_G['cache']['focus']['data'][$id]['subject']</a></dt>
    <!--{if $_G['cache']['focus']['data'][$id][image]}-->
    <dd class="m"><a href="{$_G['cache']['focus']['data'][$id]['url']}" target="_blank"><img src="{$_G['cache']['focus']['data'][$id]['image']}" alt="$_G['cache']['focus']['data'][$id]['subject']" /></a></dd>
    <!--{/if}-->
    <dd>$_G['cache']['focus']['data'][$id]['summary']</dd>
  </dl>
  <p class="ptn cl"><a href="{$_G['cache']['focus']['data'][$id]['url']}" class="xi2 y" target="_blank">{lang focus_show} &raquo;</a></p>
</div>
<!--{eval $focusi ++;}--> 
<!--{/loop}--> 
<script type="text/javascript">
			var focusnum = $focusnum;
			if(focusnum < 2) {
				$('focus_ctrl').style.display = 'none';
			}
			if(!$('focuscur').innerHTML) {
				var randomnum = parseInt(Math.round(Math.random() * focusnum));
				$('focuscur').innerHTML = Math.max(1, randomnum);
			}
			showfocus();
			var focusautoshow = window.setInterval('showfocus(\'next\', 1);', 5000);
		</script> 
<!--{/if}--> 
<!--{if $_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1}-->
<div class="focus patch" id="patch_notice"></div>
<!--{/if}--> 

<!--{ad/footerbanner/wp a_f/1}--><!--{ad/footerbanner/wp a_f/2}--><!--{ad/footerbanner/wp a_f/3}--> 
<!--{ad/float/a_fl/1}--><!--{ad/float/a_fr/2}--> 
<!--{ad/couplebanner/a_fl a_cb/1}--><!--{ad/couplebanner/a_fr a_cb/2}--> 
<!--{ad/cornerbanner/a_cn}--> 


<!--{hook/global_footer}-->
<div class="ft_wp_bg">

<div class="ft_wp">
  <div id="ft" class="wp cl">
    <div class="ft_info">
      <ul class="ft_info2">
        <li><a href="misc.php?mod=faq&action=faq&id=4" target="_blank">联系我们 </a></li>
        <li> 电话：8888 - 88888888</li>
        <li> <a target="_blank" href="http://wpa.qq.com/msgrd?v=1&uin=9490489&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:1595675868:45" alt="未来科技客服 1" title="未来科技客服 1"> 9490489 </a>&nbsp; </li>
        <li> <a target="_blank" href="http://wpa.qq.com/msgrd?v=1&uin=9490489&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:1595675868:45" alt="未来科技客服 1" title="未来科技客服 1"> 9490489 </a>&nbsp; </li>
        <li> 邮箱：9490489@qq.com </li>
      </ul>
      <ul class="ft_info3">
        <li><a href="forum.php" target="_blank"> 社区生活</a></li>
        <li><a href="#" target="_blank">谈天说地 </a></li>
        <li><a href="#" target="_blank">美景图赏</a></li>
        <li><a href="#" target="_blank">游记攻略 </a></li>
        <li><a href="#" target="_blank">帮助手册 </a></li>
      </ul>
      <ul class="ft_info4">
        <li><a href="misc.php?mod=faq&action=faq&id=1" target="_blank">关于我们</a></li>
        <li><a href="#" target="_blank">企业文化</a></li>
        <li><a href="#" target="_blank">商家入驻</a></li>
        <li><a href="#" target="_blank">广告合作</a></li>
        <li><a href="#" target="_blank">反馈建议</a></li>
      </ul>
      <ul class="ft_info1">
        <li> <a href="./" target="_blank"><img width="160" height="160" src="{$_G[style][styleimgdir]}/logo_ft.png" /> </a> </li>
      </ul>
    </div>
    
    <div class="clear"></div>
    <div  class="border_top_1" >
		<div id="flk" class="y">
			<p>
				<!--{if $_G['setting']['site_qq']}--><a href="http://wpa.qq.com/msgrd?V=3&Uin=$_G['setting']['site_qq']&Site=$_G['setting']['bbname']&Menu=yes&from=discuz" target="_blank" title="QQ"><img src="{IMGDIR}/site_qq.jpg" alt="QQ" /></a><span class="pipe">|</span><!--{/if}-->
				<!--{loop $_G['setting']['footernavs'] $nav}--><!--{if $nav['available'] && ($nav['type'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1)) ||
						!$nav['type'] && ($nav['id'] == 'stat' && $_G['group']['allowstatdata'] || $nav['id'] == 'report' && $_G['uid'] || $nav['id'] == 'archiver' || $nav['id'] == 'mobile' || $nav['id'] == 'darkroom'))}-->$nav[code]<span class="pipe">|</span><!--{/if}--><!--{/loop}-->
						<strong><a href="$_G['setting']['siteurl']" target="_blank">$_G['setting']['sitename']</a></strong>
				<!--{if $_G['setting']['icp']}-->( <a href="http://www.miitbeian.gov.cn/" target="_blank">$_G['setting']['icp']</a> )<!--{/if}-->
				<!--{hook/global_footerlink}-->
				<!--{if $_G['setting']['statcode']}-->$_G['setting']['statcode']<!--{/if}-->
			</p>
			<p class="xs0">
				{lang time_now}
				<span id="debuginfo">
				<!--{if debuginfo()}-->, Processed in $_G[debuginfo][time] second(s), $_G[debuginfo][queries] queries
					<!--{if $_G['gzipcompress']}-->, Gzip On<!--{/if}--><!--{if C::memory()->type}-->, <!--{echo ucwords(C::memory()->type)}--> On<!--{/if}-->.
				<!--{/if}-->
				</span>
			</p>
		</div>
		<div id="frt">
			<p>Powered by <strong><a href="http://www.adminbuy.cn" target="_blank">Discuz模版</a></strong> <em>$_G['setting']['version']</em><!--{if !empty($_G['setting']['boardlicensed'])}--> <a href="http://license.comsenz.com/?pid=1&host=$_SERVER[HTTP_HOST]" target="_blank">Licensed</a><!--{/if}--></p>
			<p class="xs0">&copy; 2001-2013 <a href="http://sc.adminbuy.cn" target="_blank">图标下载</a></p>
	        <p class="xs0"><a href="http://www.adminbuy.cn" target="_blank"></a></p>
		</div>
		<!--{eval updatesession();}-->
		<!--{if $_G['uid'] && $_G['group']['allowinvisible']}-->
			<script type="text/javascript">
			var invisiblestatus = '<!--{if $_G['session']['invisible']}-->{lang login_invisible_mode}<!--{else}-->{lang login_normal_mode}<!--{/if}-->';
			var loginstatusobj = $('loginstatusid');
			if(loginstatusobj != undefined && loginstatusobj != null) loginstatusobj.innerHTML = invisiblestatus;
			</script>
		<!--{/if}-->
    </div>
  </div>
</div>
</div>
<!--{/if}--> 

<!--{if !$_G['setting']['bbclosed']}--> 
<!--{if $_G[uid] && !isset($_G['cookie']['checkpm'])}--> 
<script type="text/javascript" src="home.php?mod=spacecp&ac=pm&op=checknewpm&rand=$_G[timestamp]"></script> 
<!--{/if}--> 

<!--{if $_G[uid] && helper_access::check_module('follow') && !isset($_G['cookie']['checkfollow'])}--> 
<script type="text/javascript" src="home.php?mod=spacecp&ac=follow&op=checkfeed&rand=$_G[timestamp]"></script> 
<!--{/if}--> 

<!--{if !isset($_G['cookie']['sendmail'])}--> 
<script type="text/javascript" src="home.php?mod=misc&ac=sendmail&rand=$_G[timestamp]"></script> 
<!--{/if}--> 

<!--{if $_G[uid] && $_G['member']['allowadmincp'] == 1 && !isset($_G['cookie']['checkpatch'])}--> 
<script type="text/javascript" src="misc.php?mod=patch&action=checkpatch&rand=$_G[timestamp]"></script> 
<!--{/if}--> 

<!--{/if}--> 

<!--{if $_GET['diy'] == 'yes'}--> 
<!--{if check_diy_perm($topic) && (empty($do) || $do != 'index')}--> 
<script type="text/javascript" src="{$_G[setting][jspath]}common_diy.js?{VERHASH}"></script> 
<script type="text/javascript" src="{$_G[setting][jspath]}portal_diy{if !check_diy_perm($topic, 'layout')}_data{/if}.js?{VERHASH}"></script> 
<!--{/if}--> 
<!--{if $space['self'] && CURMODULE == 'space' && $do == 'index'}--> 
<script type="text/javascript" src="{$_G[setting][jspath]}common_diy.js?{VERHASH}"></script> 
<script type="text/javascript" src="{$_G[setting][jspath]}space_diy.js?{VERHASH}"></script> 
<!--{/if}--> 
<!--{/if}--> 
<!--{if $_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1}--> 
<script type="text/javascript">patchNotice();</script> 
<!--{/if}--> 
<!--{if $_G['uid'] && $_G['member']['allowadmincp'] == 1 && empty($_G['cookie']['pluginnotice'])}-->
<div class="focus plugin" id="plugin_notice"></div>
<script type="text/javascript">pluginNotice();</script> 
<!--{/if}--> 
<!--{if $_G['uid'] && !empty($_G['cookie']['lip'])}-->
<div class="focus plugin" id="ip_notice"></div>
<script type="text/javascript">ipNotice();</script> 
<!--{/if}--> 
<!--{if $_G['member']['newprompt'] && (empty($_G['cookie']['promptstate_'.$_G[uid]]) || $_G['cookie']['promptstate_'.$_G[uid]] != $_G['member']['newprompt']) && $_GET['do'] != 'notice'}--> 
<script type="text/javascript">noticeTitle();</script> 
<!--{/if}--> 

<!--{if ($_G[member][newpm] || $_G[member][newprompt]) && empty($_G['cookie']['ignore_notice'])}--> 
<script type="text/javascript" src="{$_G[setting][jspath]}html5notification.js?{VERHASH}"></script> 
<script type="text/javascript">
	var h5n = new Html5notification();
	if(h5n.issupport()) {
		<!--{if $_G[member][newpm] && $_GET[do] != 'pm'}-->
		h5n.shownotification('pm', '$_G[siteurl]home.php?mod=space&do=pm', '<!--{avatar($_G[uid],small,true)}-->', '{lang newpm_subject}', '{lang newpm_notice_info}');
		<!--{/if}-->
		<!--{if $_G[member][newprompt] && $_GET[do] != 'notice'}-->
				<!--{loop $_G['member']['category_num'] $key $val}-->
					<!--{eval $noticetitle = lang('template', 'notice_'.$key);}-->
					h5n.shownotification('notice_$key', '$_G[siteurl]home.php?mod=space&do=notice&view=$key', '<!--{avatar($_G[uid],small,true)}-->', '$noticetitle ($val)', '{lang newnotice_notice_info}');
				<!--{/loop}-->
		<!--{/if}-->
	}
	</script> 
<!--{/if}--> 

<!--{eval userappprompt();}--> 
<!--{if $_G['basescript'] != 'userapp'}-->
<div id="scrolltop"> 
  <!--{if $_G[fid] && $_G['mod'] == 'viewthread'}--> 
  <span><a href="forum.php?mod=post&action=reply&fid=$_G[fid]&tid=$_G[tid]&extra=$_GET[extra]&page=$page{if $_GET[from]}&from=$_GET[from]{/if}" onclick="showWindow('reply', this.href)" class="replyfast" title="{lang fastreply}"><b>{lang fastreply}</b></a></span> 
  <!--{/if}--> 
  <span hidefocus="true"><a title="{lang scrolltop}" onclick="window.scrollTo('0','0')" class="scrolltopa" ><b>{lang scrolltop}</b></a></span> 
  <!--{if $_G[fid]}--> 
  <span> 
  <!--{if $_G['mod'] == 'viewthread'}--> 
  <a href="forum.php?mod=forumdisplay&fid=$_G[fid]" hidefocus="true" class="returnlist" title="{lang return_list}"><b>{lang return_list}</b></a> 
  <!--{else}--> 
  <a href="forum.php" hidefocus="true" class="returnboard" title="{lang return_forum}"><b>{lang return_forum}</b></a> 
  <!--{/if}--> 
  </span> 
  <!--{/if}--> 
</div>
<script type="text/javascript">_attachEvent(window, 'scroll', function () { showTopLink(); });checkBlind();</script> 
<!--{/if}--> 
<!--{if isset($_G['makehtml'])}--> 
<script type="text/javascript" src="{$_G[setting][jspath]}html2dynamic.js?{VERHASH}"></script> 
<script type="text/javascript">
		var html_lostmodify = {TIMESTAMP};
		htmlGetUserStatus();
		<!--{if isset($_G['htmlcheckupdate'])}-->
		htmlCheckUpdate();
		<!--{/if}-->
	</script> 
<!--{/if}--> 
<!--{eval output();}-->



<!-- 浮动在线客服代码 start -->
<!-- 默认开启 -->
<style type="text/css">
.service_online {z-index:99999; display:block; }

#vk_service { position:relative;  z-index:9999; }
.vk_sv_t { background: url($_G['style']['styleimgdir']/vk_sv_t.png) no-repeat; }
.vk_sv_m { font-size:14px; padding:5px; background: url($_G['style']['styleimgdir']/vk_sv_m.png) repeat-y; }
.vk_sv_b { width:200px; background:url($_G['style']['styleimgdir']/vk_sv_b.png) no-repeat; height:20px ; }
.vk_sv_btn_r { float:left; height:180px; margin:105px 0 0 -5px; width:50px; background:url($_G['style']['styleimgdir']/vk_sv_r.png) no-repeat; }
.vk_sv_table span { padding:5px 0px 5px 0px;  line-height: 20px; width:100px; color: #f90; font-size:14px; font-weight: bold; }
.vk_sv_table a {text-decoration:: none;   color: #0ad; font-size:14px; font-weight:bold; }
.vk_sv_table a:hover { text-decoration:: none; color: #f60; }

</style>
<div class="vk_service_online">
  <div id="vk_service" onmouseover=toBig() onmouseout=toSmall()>
    <table style=" float: left" border="0" cellspacing="0" cellpadding="0" width="200">
        <tbody>
      
      <tr>
        <td class="vk_sv_t" height="39" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td class="vk_sv_m" valign="top"><table class="vk_sv_table" border="0" cellspacing="0" cellpadding="0" width="185"  align="left">
            <tbody>
              <tr>
                <td height="15"></td>
              </tr>
              
              <tr>
                <td  align="left"> <strong>【电话】</strong><span>(此处填写电话号码)</span> </td>
              </tr>
              <tr>
                <td height="25"></td>
              </tr>
              
              <!-- QQ客服，直接把下列两处QQ号码修改成您的即可 ，或者到 http://wp.qq.com/index.html 选择自己喜欢的风格，然后替换掉下面的链接代码 -->
              <tr>
                <td  align="left"> <strong>【QQ】</strong> <a target="_blank" href="http://wpa.qq.com/msgrd?v=1&amp;uin=1595675868&amp;site=qq&amp;menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:1595675868:45" alt="未来科技" title="未来科技"> 1595675868</a>
 </td>
              </tr>
              <tr>
                <td height="15"></td>
              </tr>
              


              
              <!-- 旺旺客服，请到 http://wangwang.taobao.com/2010_fp/world.php 申请客服代码，把下列链接改成自己的即可 -->
              <tr>
                <td  align="left"> <strong>【旺旺】</strong>
                <a target="_blank" href="#" >旺旺账号</a>
              </td>
              </tr>
              <tr>
                <td height="15"></td>
              </tr>

              
              <tr>
                <td  align="left"> <strong>【邮箱】 </strong><br/><span> company@gmail.com </span><br/> </td>
              </tr>

              <tr>
                <td height="15"></td>
              </tr>
              
              
              <tr>
                <td  align="left"> <strong>【地址】</strong> （请填写公司地址）</td>
              </tr>
              <tr>
                <td height="25"></td>
              </tr>
              
              
              <tr>
                <td  align="left"> <a href="http://www.veikei.com" target="_blank">【未来科技】</a><br/>
                <a href="http://www.veikei.com" target="_blank">【www.veikei.com】</a></td>
              </tr>
              <tr>
                <td height="25"></td>
              </tr>
 
             
            <tr>
              <td align="middle">&nbsp;</td>
            </tr>
              </tbody>
          </table></td>
      </tr>
      <tr>
        <td class="vk_sv_b" valign="top"></td>
      </tr>
        </tbody>
    </table>
    <div class="vk_sv_btn_r"></div>
  </div>
  <script language=javascript>
		客服=function (id,_top,_left){
		var me=id.charAt?document.getElementById(id):id, d1=document.body, d2=document.documentElement;
		d1.style.height=d2.style.height='100%';me.style.top=_top?_top+'px':0;me.style.left=_left+"px";//[(_left>0?'left':'left')]=_left?Math.abs(_left)+'px':0;
		me.style.position='absolute';
		setInterval(function (){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));
		return arguments.callee;
		};
		window.onload=function (){
		客服
		('vk_service',100,-200)
		}
	</script>
  <script language=javascript> 
			lastScrollY=0; 
			
			var InterTime = 1;
			var maxWidth=-1;
			var minWidth=-200;
			var numInter = 8;
			
			var BigInter ;
			var SmallInter ;
			
			var o =  document.getElementById("vk_service");
				var i = parseInt(o.style.left);
				function Big()
				{
					if(parseInt(o.style.left)<maxWidth)
					{
						i = parseInt(o.style.left);
						i += numInter;	
						o.style.left=i+"px";	
						if(i==maxWidth)
							clearInterval(BigInter);
					}
				}
				function toBig()
				{
					clearInterval(SmallInter);
					clearInterval(BigInter);
						BigInter = setInterval(Big,InterTime);
				}
				function Small()
				{
					if(parseInt(o.style.left)>minWidth)
					{
						i = parseInt(o.style.left);
						i -= numInter;
						o.style.left=i+"px";
						
						if(i==minWidth)
							clearInterval(SmallInter);
					}
				}
				function toSmall()
				{
					clearInterval(SmallInter);
					clearInterval(BigInter);
					SmallInter = setInterval(Small,InterTime);
					
				}
				
</script> 
</div>

<!-- 浮动在线客服代码 end -->




<style type="text/css">
.vk_kefu_bt {position:fixed; bottom:0; z-index:9999; width:100%; height:60px; background:#111;}
	.vk_kefu_bottom_ct { width:1200px;  margin:0 auto;  color:#fff; font-size:14px; line-height:24px; }
		.vk_kefu_bottom_ct a { color:#fc0; }
		.vk_kefu_bottom_ct span { color:#fc0; }
		.vk_bt_left { margin-top:-15px; margin-right:15px; }
		.vk_bt_right {padding:5px 0 0 0;}
</style>

<div class="vk_kefu_bt">
<div class="vk_kefu_bottom_ct">
	<!-- 此处的QQ号码 1595675868 改成您的QQ号码即可 -->
    <div class="z vk_bt_left"> <a target="_blank" href="http://wpa.qq.com/msgrd?v=1&uin=1595675868&site=qq&menu=yes"><img border="0" src="$_G['style']['styleimgdir']/vk_kefu_bt_qq.png" alt="在线客服" title="在线客服"> </a></div>
    
    <div class="z vk_bt_left"> <img border="0" src="$_G['style']['styleimgdir']/vk_kefu_bt_phone.png" alt="免费咨询电话" title="免费咨询电话">  </div>
    
    <div class="y vk_bt_right">
        <p> 电话：<span> 8888 - 88888888 </span></p>
    	<p> 地址： <span> <a href="http://www.veikei.com" target="_blank">未来科技  </a>（ 此处填写您的地址 ）</span></p>
    </div>
</div>
</div>



</body></html>