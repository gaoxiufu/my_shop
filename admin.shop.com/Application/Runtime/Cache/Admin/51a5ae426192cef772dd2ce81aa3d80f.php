<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - 商品列表 </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="http://admin.shop.com/Public/css/general.css" rel="stylesheet" type="text/css" />
<link href="http://admin.shop.com/Public/css/main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="http://admin.shop.com/Public/css/page.css" />
</head>
<body>
<h1>
    <span class="action-span"><a href="<?php echo U('add');?>">添加商品</a></span>
    <span class="action-span1"><a href="#">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - 商品列表 </span>
    <div style="clear:both"></div>
</h1>
<div class="form-div">
    <form action="" name="searchForm">
        <img src="http://admin.shop.com/Public/images/icon_search.gif" width="26" height="22" border="0" alt="search" />
        <!-- 分类 -->
        <?php echo array_select($goods_categorys,'name','id', 'goods_category_id', I('get.goods_category_id'));?>
        <!-- 品牌 -->
        <?php echo array_select($brands,'name','id', 'brand_id', I('get.brand_id'));?>
        <!-- 推荐 -->
        <?php echo array_select($goods_statuses,'name','id', 'goods_status', I('get.goods_status'));?>
        <!-- 上架 -->
        <?php echo array_select($is_on_sales,'name','id', 'is_on_sale', I('get.is_on_sale'));?>
        <!-- 关键字 -->
        关键字 <input type="text" name="name" size="15" value="<?php echo I('get.name');?>" />
        <input type="submit" value=" 搜索 " class="button" />
    </form>
</div>

<!-- 商品列表 -->
<form method="post" action="" name="listForm" onsubmit="">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>编号</th>
                <th>商品名称</th>
                <th>货号</th>
                <th>市场价/本店价</th>
                <th>上架</th>
                <th>精品</th>
                <th>新品</th>
                <th>热销</th>
                <th>推荐排序</th>
                <th>库存</th>
                <th>操作</th>
            </tr>
            <?php if(is_array($rows)): foreach($rows as $key=>$row): ?><tr>
                <td align="center"><?php echo ($key+1); ?></td>
                <td align="center" class="first-cell"><span><?php echo ($row["name"]); ?></span></td>
                <td align="center"><span onclick=""><?php echo ($row["sn"]); ?></span></td>
                <td align="center"><span><?php echo ($row["market_price"]); ?>/<?php echo ($row["shop_price"]); ?></span></td>
                <td align="center">
                    <img src="http://admin.shop.com/Public/images/<?php if($row['is_on_sale'] == 1): ?>yes.gif<?php else: ?>no.gif<?php endif; ?>"/>
                </td>
                <td align="center">
                    <img src="http://admin.shop.com/Public/images/<?php if($row['is_best'] == 1): ?>yes.gif<?php else: ?>no.gif<?php endif; ?>"/>
                </td>
                <td align="center">
                    <img src="http://admin.shop.com/Public/images/<?php if($row['is_new'] == 1): ?>yes.gif<?php else: ?>no.gif<?php endif; ?>"/>
                </td>
                <td align="center">
                    <img src="http://admin.shop.com/Public/images/<?php if($row['is_hot'] == 1): ?>yes.gif<?php else: ?>no.gif<?php endif; ?>"/>
                </td>
                <td align="center"><?php echo ($row["sort"]); ?></td>
                <td align="center"><?php echo ($row["stock"]); ?></td>
                <td align="center">
                <a href="<?php echo U('edit',['id'=>$row['id']]);?>" title="编辑"><img src="http://admin.shop.com/Public/images/icon_edit.gif" width="16" height="16" border="0" /></a>
                <a href="<?php echo U('remove',['id'=>$row['id']]);?>" onclick="" title="回收站"><img src="http://admin.shop.com/Public/images/icon_trash.gif" width="16" height="16" border="0" /></a></td>
            </tr><?php endforeach; endif; ?>
        </table>

    <!-- 分页开始 -->

        <!-- 分页结束 -->
    </div>
    <div class="digg">
        <?php echo ($page_html); ?>
    </div>
</form>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>