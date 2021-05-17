<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/21
 * Time: 19:57
 */

namespace App\Admin\Controller;


use App\Admin\Model\Article;
use CK\Core\Controller;
use CK\Util\Cipher;

/**
 * 管理端文章管理
 * Class ArticleManage
 * @res true
 * @package App\Admin\Controller
 */
class ArticleManage extends Controller {

	public function query($query, $page, $num) {
		$fields = [
			'art_id'=>'',
			'art_title'=>'',
			'art_type'=>'',
			'ctg_name'=>'',
			'art_created_date'=>'',
		];

		$where = [];
		foreach ($query as $result) {
			if (!empty($result['value'])) {
				$column = $result['name']."[{$result['type']}]";
				$where[$column] = $result['value'];
			}
		}

		$result = Article::inst()->query($fields, $where, null, null, $num, $page, function ($row) {
			$row['cipher_id'] = Cipher::inst()->encrypt($row['art_id']);
			switch ($row['art_type']) {
				case 1:
					$row['art_type_text'] = '文字';
					break;
				case 2:
					$row['art_type_text'] = '图片';
					break;
				case 3:
					$row['art_type_text'] = '链接新闻';
					break;
				default:
					$row['art_type_text'] = '未知';
			}
			return $row;
		});
		return $result;
	}
}