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
            'ctg_tmp_id'=>'',
            'created_date'=>''
        ];

        $where = [];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                $column = $result['name']."[{$result['type']}]";
                $where[$column] = $result['value'];
            }
        }

        $result = ArticleCategory::inst()->query($fields,$where,null,null,$num,$page,function($row){
            $row['cipher_id'] = Cipher::inst()->encrypt($row['tmp_id']);
            return $row;
        });
        return $result;
    }
}