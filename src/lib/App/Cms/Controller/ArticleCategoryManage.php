<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/20
 * Time: 15:18
 */

namespace App\Cms\Controller;


use App\Cms\Model\Article;
use App\Cms\Model\ArticleCategory;
use App\Cms\Model\Tag;
use App\Cms\Model\Template;
use CK\Core\Controller;
use CK\Util\Cipher;
use CK\Util\Pinyin;

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
    public function query($query, $num, $page) {
        $fields = [
            'ctg_id' => '',
            'ctg_name' => '',
            'ctg_parent_id' => '',
            'ctg_parent_name' => '',
            'ctg_publish_path' => '',
            'tmp_id' => '',
            'tmp_name' => '',
            'created_date' => '',
            'modified_date' => ''
        ];

        $where = [];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                if (empty($result['type'])) {
                    $column = $result['name'];
                } else {
                    $column = $result['name'] . "[{$result['type']}]";
                }
                $where[$column] = $result['value'];
            }
        }

        $result = ArticleCategory::inst()->query($fields, $where, null, null, $num, $page, function ($row) {
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
    public function info($cipher_id) {
        $ctg_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($ctg_id)) {
            $category = ArticleCategory::inst()->find(['ctg_id' => $ctg_id]);
            return $category;
        }
        return false;
    }

    /**
     * 获取汉字拼音
     * @param $keyword
     *
     * @res true
     * @return string
     */
    public function getPinyin($keyword) {
        return Pinyin::inst()->getAll($keyword);
    }

    /**
     * 添加和修改分类
     * @param $data
     *
     * @res true
     * @return bool
     */
    public function save($data) {
        $template = Template::inst()->find(['tmp_id' => $data['tmp_id']]);
        if ($template) {
            $data['tmp_name'] = $template['tmp_name'];
        }
        if (empty($data['ctg_id'])) {
            unset($data['ctg_id']);
            $data['created_date'] = CK_NOW;
            $rel = ArticleCategory::inst()->insert($data);
            return $rel;
        } else {
            $data['modified_date'] = CK_NOW;
            $rel = ArticleCategory::inst()->update($data, ['ctg_id' => $data['ctg_id']]);
            return $rel;
        }
    }

    /**
     * 删除分类
     * @param $cipher_id
     *
     * @res true
     * @return bool
     */
    public function delete($cipher_id) {
        $ctg_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($ctg_id)) {
            //判断该分类下面是否有文章，如果有则不能删除
            $article = Article::inst()->find([
                'ctg_id' => $ctg_id,
                'is_del' => 0
            ]);
            //判断该分类下面是否有子分类，如果有则不能删除
            $category = ArticleCategory::inst()->find(['ctg_parent_id' => $ctg_id]);
            print_r($category);die;
            if ($article) {
                return ['error' => 40001];
            } elseif ($category) {
                return ['error' => 40002];
            }
            $rel = ArticleCategory::inst()->delete(['ctg_id' => $ctg_id]);
            return $rel;
        }
        return false;
    }

    /**
     *标签查询
     * @param $num
     * @param $page
     *
     * @res true
     * @return array
     */
    public function getTags($num, $page) {
        $fields = [
            'tag_id' => '',
            'tag_name' => '',
            'tag_num' => '',
            'tag_created_date' => ''
        ];

        $where = [];

        $result = Tag::inst()->query($fields, $where, null, null, $num, $page, function ($row) {
            $row['cipher_id'] = Cipher::inst()->encrypt($row['tag_id']);
            return $row;
        });
        return $result;
    }
}