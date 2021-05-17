<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/20
 * Time: 15:18
 */

namespace App\Cms\Controller;


use App\Cms\Model\ArticleCategory;
use CK\Core\Controller;
use CK\Util\Cipher;

/**
 * 文章分类管理操作类
 * Class ArticleCategoryManage
 *
 * @res true
 * @package App\Cms\Controller
 */
class ArticleCategoryManage extends Controller {

    /**
     * 查询文章分类
     * @param $query
     * @param $num
     * @param $page
     *
     * @res true
     * @return array
     */
    public function query($query,$num,$page) {
        $fields = [
            'ctg_id'=>'',
            'ctg_name'=>'',
            'ctg_publish_path'=>'',
            'tmp_id'=>'',
            'tmp_name'=>'',
            'created_date'=>'',
            'modified_date'=>''
        ];

        $where = [];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                $column = $result['name']."[{$result['type']}]";
                $where[$column] = $result['value'];
            }
        }

        $result = ArticleCategory::inst()->query($fields,$where,null,null,$num,$page,function($row){
            $row['cipher_id'] = Cipher::inst()->encrypt($row['ctg_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 获取分类详情
     * @param $cipher_id
     *
     * @res true
     * @return array|bool
     */
    public function info($cipher_id){
        $ctg_id = Cipher::inst()->decrypt($cipher_id);
        if(!empty($ctg_id)){
            $category = ArticleCategory::inst()->find(['ctg_id'=>$ctg_id]);
            return $category;
        }
        return false;
    }

    /**
     * 添加和修改分类
     * @param $data
     *
     * @res true
     * @return bool
     */
    public function save($data){
        if(empty($data['ctg_id'])){
            unset($data['ctg_id']);
            $data['create_date'] = CK_NOW;
            $rel = ArticleCategory::inst()->insert($data);
            return $rel;
        }else{
            $data['modified_date'] = CK_NOW;
            $rel = ArticleCategory::inst()->update($data,['ctg_id'=>$data['ctg_id']]);
            return $rel;
        }
    }

}