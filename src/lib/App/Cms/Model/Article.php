<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/26
 * Time: 00:49
 */

namespace App\Cms\Model;


use CK\Core\Model;

class Article extends Model{

    public function __construct() {
        parent::__construct('t_article');
    }


	/**
	 * 通过文章id获取一条文章详细数据
	 * @param $art_id
	 * @return array
	 */
	public function getArtById($art_id)
	{
		$this->_table->clear();
		$result = $this->_table
			->where(['art_id' => $art_id])
			->execute()->
			get_result_one();
		return $result;
	}

}