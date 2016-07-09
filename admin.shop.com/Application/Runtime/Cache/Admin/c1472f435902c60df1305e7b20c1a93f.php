<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>ECSHOP 管理中心 - 编辑商品 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="http://admin.shop.com/Public/css/general.css" rel="stylesheet" type="text/css"/>
    <link href="http://admin.shop.com/Public/css/main.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="http://admin.shop.com/Public/ext/ztree/css/zTreeStyle/zTreeStyle.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="http://admin.shop.com/Public/ext/uploadify/uploadify.css" />
    <style type="text/css">
        .upload-pre-item img {
            width: 150px;
        }

        .upload-pre-item {
            display: inline-block;
        }

        .upload-pre-item a {
            position: relative;
            top: 5px;
            right: 15px;
            float: right;
            color: red;
            font-size: 16px;
            text-decoration: none;
        }
        ul.ztree{
            margin-top: 10px;
            border: 1px solid #617775;
            background: #f0f6e4;
            width: 220px;
            overflow-y: scroll;
            overflow-x: auto;
        }
        .gao {
            border: 1px red solid;
            color: red;
        }
    </style>
</head>
<body>
<h1>
    <span class="action-span"><a href="<?php echo U('index');?>">商品列表</a>
    </span>
    <span class="action-span1"><a href="#">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - 编辑商品 </span>

    <div style="clear:both"></div>
</h1>

<div class="tab-div">
    <div id="tabbody-div">
        <form enctype="multipart/form-data" action="<?php echo U();?>" method="post">
            <table width="90%" id="general-table" align="center">
                <tr>
                    <td class="label">商品名称：</td>
                    <td><input type="text" name="name" value="<?php echo ($row["name"]); ?>" size="30"/>
                        <span class="require-field">*</span></td>
                </tr>
                <tr>
                    <td class="label">LOGO：</td>
                    <td>
                        <input type="hidden" name="logo" id="logo" value="<?php echo ($row["logo"]); ?>" size="30" />
                        <img src="<?php echo ($row["logo"]); ?>" id="logo-preview"/>
                        <input type="file" id="goods_logo"/>
                </tr>
                <tr>
                    <td class="label">商品货号：</td>
                    <td>
                        <?php if(isset($row)): ?><input type="text" name="sn" disabled='disabled' value="<?php echo ($row["sn"]); ?>" size="20"/>
                            <?php else: ?>
                            <input type="text" name="sn" disabled='disabled' value="<?php echo ($sn); ?>" size="20"/><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">商品分类：</td>
                    <td>
                        <input type="text" disabled='disabled' id="goods_category_name">
                        <input type="hidden"  name="goods_category_id" id="goods_category_id">
                        <ul id='goods_categorys' class='ztree'></ul>
                    </td>
                </tr>
                <tr>
                    <td class="label">商品品牌：</td>
                    <td>
                        <?php echo array_select($brands, 'name', 'id', 'brand_id', $row['brand_id']);?>
                    </td>
                </tr>
                <tr>
                    <td class="label">供货商：</td>
                    <td>
                        <?php echo array_select($suppliers, 'name', 'id', 'supplier_id',$row['supplier_id']);?>
                    </td>
                </tr>
                <tr>
                    <td class="label">本店售价：</td>
                    <td>
                        <input type="text" name="shop_price" value="<?php echo ($row["shop_price"]); ?>" size="20"/>
                        <span class="require-field">*</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">市场售价：</td>
                    <td>
                        <input type="text" name="market_price" value="<?php echo ($row["market_price"]); ?>" size="20"/>
                    </td>
                </tr>
                <tr>
                    <td class="label">商品数量：</td>
                    <td>
                        <input type="text" name="stock" size="8" value="<?php echo ((isset($row["stock"]) && ($row["stock"] !== ""))?($row["stock"]):100); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="label">是否上架：</td>
                    <td>
                        <input type="radio" name="is_on_sale" value="1" class="is_on_sale"/> 是
                        <input type="radio" name="is_on_sale" value="0" class="is_on_sale"/> 否
                    </td>
                </tr>
                <tr>
                    <td class="label">加入推荐：</td>
                    <td>
                        <input type="checkbox" name="goods_status[]" value="1" class="goods_status"/> 精品
                        <input type="checkbox" name="goods_status[]" value="2" class="goods_status"/> 新品
                        <input type="checkbox" name="goods_status[]" value="4" class="goods_status"/> 热销
                    </td>
                </tr>
                <tr>
                    <td class="label">推荐排序：</td>
                    <td>
                        <input type="text" name="sort" size="5" value="<?php echo ((isset($row["sort"]) && ($row["sort"] !== ""))?($row["sort"]):100); ?>"/>
                    </td>
                </tr>


                <tr>
                    <td class="label">商品详细描述：</td>
                    <td >
                        <textarea name="content" cols="40" rows="3" id='editor'><?php echo ($row["content"]); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <hr/>
                    </td>
                </tr>

                <tr>
                    <td class="label">商品相册：</td>
                    <td>
                        <div class="upload-img-box">

                            <?php if(is_array($row["paths"])): foreach($row["paths"] as $key=>$path): ?><div class="upload-pre-item">
                                <img src="<?php echo ($path); ?>"/>
                                <a href="#" data="<?php echo ($key); ?>">×</a>
                            </div><?php endforeach; endif; ?>
                        </div>

                        <div>
                            <input type="file" id="goods_gallery"/>
                        </div>
                    </td>
                </tr>
            </table>


            <div class="button-div">
                <input type="hidden" name="id" value="<?php echo ($row["id"]); ?>"/>
                <input type="submit" value=" 确定 " class="button"/>
                <input type="reset" value=" 重置 " class="button"/>
            </div>
        </form>
    </div>
</div>

<div id="footer">
    共执行 9 个查询，用时 0.025161 秒，Gzip 已禁用，内存占用 3.258 MB<br/>
    版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。
</div>
<script type="text/javascript" charset="utf-8" src="http://admin.shop.com/Public/ext/ueditor/my.config.js"></script>
<script type="text/javascript" charset="utf-8" src="http://admin.shop.com/Public/ext/ueditor/ueditor.all.min.js"> </script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="http://admin.shop.com/Public/ext/ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="http://admin.shop.com/Public/js/jquery.min.js"></script>
<script type="text/javascript" src="http://admin.shop.com/Public/ext/ztree/js/jquery.ztree.core.js"></script>
<script type="text/javascript" src="http://admin.shop.com/Public/ext/uploadify/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="http://admin.shop.com/Public/ext/layer/layer.js"></script>
<script type="text/javascript">
    ///////////////////  UEeditor开始 ///////////////////////////////////

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
//    var ue = UE.getEditor('editor',{serverUrl: '<?php echo U('Editor/ueditor');?>');
    var ue = UE.getEditor('editor',{serverUrl: '<?php echo U('Editor/ueditor');?>'});
    ///////////////////  UEeditor结束

    ///////////////////  ztree开始 ///////////////////////////////////
    var setting = {
        data: {
            simpleData: {
                enable: true,
                pIdKey: "parent_id",
            }
        },
        callback:{
            onClick:function(event,node,item){
                //取出点击节点的数据，放到表单节点中
                $('#goods_category_id').val(item.id);
                $('#goods_category_name').val(item.name);
            },
        },
    };

    // 接收json 数据
    var goods_categorys = <?php echo ($goods_categorys); ?>;
   // 初始化ztree
    $(function(){

        var goods_ztree=$.fn.zTree.init($("#goods_categorys"), setting, goods_categorys);
        // 展开所有节点
        goods_ztree.expandAll(true);
    //回显父级分类
    <?php if(isset($row)): ?>//获取父级分类在ztree中的节点
    var parent_node = goods_ztree.getNodeByParam('id',<?php echo ($row["goods_category_id"]); ?>);
    goods_ztree.selectNode(parent_node);
    $('#goods_category_id').val(parent_node.id);
    $('#goods_category_name').val(parent_node.name);<?php endif; ?>
    });
    ////////////////////////////    ztree  结束
    ////////////////////////////    绑定回显状态
    $('.is_on_sale').val([<?php echo ((isset($row["is_on_sale"]) && ($row["is_on_sale"] !== ""))?($row["is_on_sale"]):1); ?>]);
    $('.goods_status').val(<?php echo ((isset($row["goods_status"]) && ($row["goods_status"] !== ""))?($row["goods_status"]):'[1]'); ?>);

    ////////////////////////////    uploadify  开始
    // 使用uploadify
    $options = {
        'swf': 'http://admin.shop.com/Public/ext/uploadify/uploadify.swf',
        'uploader': '<?php echo U("Upload/uploadImg");?>',
        'buttonClass': 'gao', //自定义样式
        'buttonText': '选择图片',
        'fileObjName': 'file_data',
        'onUploadSuccess': function (file_itme, response) {
            //console.debug(arguments);
            var data = $.parseJSON(response);
            if (data.status) {
                var html='<div class="upload-pre-item">\
                        <img src="'+data.file_url+'"/>\
                        <a href="#">×</a>\
                        <input type="hidden" name="path[]" value="'+data.file_url+'"/>\
                </div>';
                $(html).appendTo($('.upload-img-box'));
//                $('#logo').val(data.file_url);
//                $('#logo_preview').attr('src',data.file_url);
                layer.alert(data.msg, {icon: 6});
            } else {
                layer.alert(data.msg, {icon: 5});
            }
        },
    };
    $('#goods_gallery').uploadify($options);
//////////////////////////////// ajax 删除相册
    // 事件委派
    $('.upload-img-box').on('click','a',function(){
        //删除图片
        //通过a标签上的data属性来判断是不是新上传的
        var id = $(this).attr('data');
        var flag = true;
        var parent_node = $(this).parent();
        //删除数据库的记录,使用ajax
        if(id){
            var url = '<?php echo U("Goods/removeGallery");?>';
            var data = {
                id:id,
            };
            $.getJSON(url,data,function(response){
                console.debug(response)
                             //判断是否删除成功
                if(!response.status){
                    flag = false;
                }
            });
        }
        if(flag){
            //移除div
            parent_node.remove();
            layer.alert('删除成功',{icon:6});
        }else{
            layer.alert('删除失败',{icon:5});
        }
        return false;   //阻止事件冒泡
    });
    //========================  使用uploadify上传logo   ========================
    logo_options = {
        swf:'http://admin.shop.com/Public/ext/uploadify/uploadify.swf',
        uploader:'<?php echo U("Upload/uploadImg");?>',
        buttonText:' 选择文件 ',
        fileObjName:'file_data',
        onUploadSuccess:function(file_item,response){
            //根据返回的status来判定是否成功
            var data = $.parseJSON(response);
            if(data.status){
                $('#logo').val(data.file_url);
                $('#logo-preview').attr('src',data.file_url);
                layer.alert(data.msg, {icon: 6});
            }else{
                layer.alert(data.msg, {icon: 5});
            }
        },
    };
    $('#goods_logo').uploadify(logo_options);
</script>
</body>
</html>