<?php

/**
 * 友情链接管理
 * User: liberty.lai
 * Date: 2017/2/22
 * Time: 17:42
 */

use CK\Util\Cipher;

/**
 * @res true
 * Class LinkManage
 */
class LinkManage extends \CK\Core\Controller
{
    /**
     * 查询友情链接列表
     * @param $query
     * @param $num
     * @param $page
     *
     * @res true
     * @return array
     */
    public function queryLink($query, $num, $page)
    {
        $fields = [
            'link_id' => '',
            'ctg_id' => '',
            'ctg_name' => '',
            'link_name' => '',
            'link_addr' => '',
            'link_type' => '',
            'link_img' => '',
            'link_img_width' => '',
            'link_img_height' => '',
            'create_date' => '',
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
        $result = Link::inst()->query($fields, $where, null, null, $num, $page, function ($row) {
            $row['cipher_id'] = Cipher::inst()->encrypt($row['link_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 查询友情导航分类
     * @param $query
     * @param $num
     * @param $page
     *
     * @res true
     * @return array
     */
    public function queryCategory($query, $num, $page)
    {
        $fields = [
            'ctg_id' => '',
            'ctg_name' => '',
            'create_date' => '',
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
        $result = LinkCategory::inst()->query($fields, $where, null, null, $num, $page, function ($row) {
            $row['cipher_id'] = Cipher::inst()->encrypt($row['link_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 获取友情链接详情
     * @param $cipher_id
     *
     * @res true
     * @return array|bool
     */
    public function linkInfo($cipher_id)
    {
        $link_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($link_id)) {
            $link = Link::inst()->find(['link_id' => $link_id]);
            return $link;
        }
        return false;
    }

    /**
     * 获取友情链接分类
     * @param $cipher_id
     *
     * @res true
     * @return array|bool
     */
    public function categoryInfo($cipher_id)
    {
        $link_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($link_id)) {
            $link = LinkCategory::inst()->find(['link_id' => $link_id]);
            return $link;
        }
        return false;
    }

    /**
     * 添加和修改友情链接
     * @param $data
     *
     * @res true
     * @return bool
     */
    public function saveLink($data)
    {
        if (empty($data['link_id'])) {
            unset($data['link_id']);
            $data['created_date'] = CK_NOW;
            $rel = Link::inst()->insert($data);
            return $rel;
        } else {
            $data['modified_date'] = CK_NOW;
            $rel = Link::inst()->update($data, ['link_id' => $data['link_id']]);
            return $rel;
        }
    }

    /**
     * 添加和修改友情链接分类
     * @param $data
     *
     * @res true
     * @return bool
     */
    public function saveCategory($data)
    {
        if (empty($data['ctg_id'])) {
            unset($data['ctg_id']);
            $data['created_date'] = CK_NOW;
            $rel = LinkCategory::inst()->insert($data);
            return $rel;
        } else {
            $data['modified_date'] = CK_NOW;
            $rel = LinkCategory::inst()->update($data, ['ctg_id' => $data['ctg_id']]);
            return $rel;
        }
    }

    /**
     * 删除友情链接
     * @param $cipher_id
     *
     * @res true
     * @return bool
     */
    public function deleteLink($cipher_id)
    {
        $link_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($link_id)) {
            $rel = Link::inst()->delete(['link_id' => $link_id]);
            return $rel;
        }
        return false;
    }
    /**
     * 删除分类
     * @param $cipher_id
     *
     * @res true
     * @return bool
     */
    public function deleteCategory($cipher_id)
    {
        $ctg_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($ctg_id)) {
            $rel = LinkCategory::inst()->delete(['ctg_id' => $ctg_id]);
            return $rel;
        }
        return false;
    }




}