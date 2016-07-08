<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7
 * Time: 23:17
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model
{
    public function getHelpArticle()
    {
        // 获取所有文字分类
        $article_category_model = M('ArticleCategory');
        $article_categorys = $article_category_model->where(['status' => 1, 'is_help' => 1])->getField('id,name');
        // 获取分类下的文章
        $datas = [];
        foreach ($article_categorys as $key => $value) {
//            $articles=$this->where(['status'=>1,'article_category_id'=>$key])->order('sort')->limit(6)->getField('id,name');
            $articles = $this->field('id,name')->order('sort')->limit(6)->where(['status' => 1, 'article_category_id' => $key])->select();
            $datas[$value] = $articles;
        }
        return $datas;
    }

}