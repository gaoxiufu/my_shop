<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/25
 * Time: 14:30
 */

namespace Admin\Model;


use Think\Model;

class ArticleModel extends Model
{
    protected $patchValidate = true;//开启批量验证
    /**
     * 自动验证
     */
    protected $_validate = [
        ['name', 'require', '文章名不能为空'],
        ['name', '', '文章已存在', self::EXISTS_VALIDATE, 'unique'],
        ['status', '0,1', '状态不合法', self::EXISTS_VALIDATE, 'in'],
        ['sort', 'number', '排序必须为数字'],
        ['article_category_id', '1,2,3,4,5,6,7,8', '文章类别不合法', self::EXISTS_VALIDATE, 'in'],
    ];

    // 自动完成
    protected $_auto = [
        // ['password','md5',self::MODEL_INSERT,'function','register'],
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
    ];

    /**
     *自定义getListPage方法
     * 1.模糊搜索
     * 2.分页
     * 3.重新组装数据
     * @return mixed
     */
    public function getListPage($cont)
    {
        // 获取文章类别表数据
        $model = M('ArticleCategory');
        //$datas = $model->select();
        // 获取文章表数据总条数
        $count = $this->where($cont)->count();
        // 调用分页工具
        $page = new \Think\Page($count, 5);
        // 获取分页样式
        $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        // 获取分页代码
        $html = $page->show();
        // 获取分页数据
        $rows = $this->where($cont)->page(I('get.p', 1), 5)->select();
        // 获取文章表数据
        // $rows = $this->select();
        // 循环文章类别表和文章表，如果文章类别ID相等，就将文章类别名赋值给category字段
        foreach ($rows as &$row) {
            $row['category'] = $model->getFieldById($row['article_category_id'], 'name');
//            foreach ($datas as $data) {
//                if ($data['id'] == $row['article_category_id']) {
//                    $row['category'] = $data['name'];
//                }
//            }
        }
        // 返回重新组织的数据和分页代码
        return compact('rows', 'html');
    }

    /**
     * 自定义addArticle方法，把数据分布插入文章表和文章内容表
     * @param $content
     * @return bool
     */
    public function addArticle($content)
    {
        // 开启事务
        $this->startTrans();

        // 现在文章信息并返回其ID
        $article_id = $this->add();
        if ($article_id === false) { // 如果文章信息表数据新增失败就回滚事务
            // 回滚事务
            $this->rollback();
            return false;

        }
        // 准备文章内容表示需要的数据
        $data = [
            'content'    => $content,
            'article_id' => $article_id
        ];

        if (!empty($data)) { // 检测$data
            // 实例化文章内容表
            $content_model = M('ArticleContent');
            // 将数据插入到文章内容表
            $result = $content_model->add($data);
            if ($result === false) { // 如果数据插入文章内容表失败，就回滚事务
                // 回滚事务
                $this->rollback();
                return false;
            }
        }
        // 提交事务
        $this->commit();
    }

    /**
     * 准备修改回显数据
     * @param $id
     * @return array
     */
    public function selectArticle($id)
    {
        // 获取文章数据
        $row = $this->find($id);
        // 获取文章分类
        $type_model = M('article_category');
        $row['category'] = $type_model->getFieldById($row['article_category_id'], 'name');
        $types = $type_model->select();
//        foreach ($types as $type) {
//
//            if ($type['id'] == $row['article_category_id']) {
//                $row['category'] = $type['name'];
//            }
//        }

        // 获取文章内容
        $content_model = M('article_content');
        $content = $content_model->find($id);
        return compact('row', 'content', 'types');
    }

    /**
     * 更新文章表和文章内容表
     * @param $content
     */
    public function saveArticle($content)
    {
        // 开启事务
        $this->startTrans();
        // 获取id
        $article_id = $this->data['id'];
        $res = $this->save();
        if ($res === false) {
            // 回滚事务
            $this->rollback();
            exit;
        }

        // 准备文章内容表更新的数据
        $data = [
            'article_id' => $article_id,
            'content'    => $content,
        ];
        if (!empty($data)) {
            // 实例化文章内容表
            $content_model = M('ArticleContent');
            // 将数据插入到文章内容表
            $result = $content_model->save($data);
            if ($result === false) { // 如果数据插入文章内容表失败，就回滚事务
                // 回滚事务
                $this->rollback();
                exit;
            }
        }
        // 提交事务
        $this->commit();
    }

    public function deleteArticle($id)
    {
        $this->delete($id);
        // 实例化文章内容表
        $content_model = M('ArticleContent');
        $content_model->delete($id);
    }

}
