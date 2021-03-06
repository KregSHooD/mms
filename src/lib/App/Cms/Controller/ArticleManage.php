<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/21
 * Time: 19:57
 */

namespace App\Cms\Controller;


use App\Cms\Model\Article;
use CK\Core\Controller;
use CK\Util\Cipher;
use Common\Component\Upload;

/**
 * 管理端文章管理
 * Class ArticleManage
 * @res true
 * @package App\Cms\Controller
 */
class ArticleManage extends Controller {

	/**
	 * 分页获取文章数据
	 * @param $query
	 * @param $page
	 * @param $num
	 * @res true
	 *
	 * @return array
	 */
	public function query($query, $page, $num) {
		$fields = [
			'art_id' => '',
			'art_title' => '',
			'art_type' => '',
			'ctg_name' => '',
			'art_created_date' => '',
		];

		$where = [];
		foreach ($query as $result) {
			if (!empty($result['value'])) {
				$column = $result['name'] . "[{$result['type']}]";
				$where[$column] = $result['value'];
			}
		}

		$result = Article::inst()->query($fields, $where, null, ['art_id', 'DESC'], $num, $page, function ($row) {
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

	/**
	 * 保存修改文章
	 * @res true
	 *
	 * @param $data
	 * @return bool
	 */
	public function save($data) {
		$flag = false;
		if (!empty($data['upload_file'])) {
			$data['art_img'] = Upload::inst()->update(APP_PATH . substr($data['upload_file'], 1));
		}
		$cont_str = $data['art_content'];
		preg_match_all("/src=('|\")(\/asset(.*?))('|\").*?(\/>|>)/", $cont_str, $img_src);
		if (!empty($img_src[2])) {
			foreach ($img_src[2] as $src) {
				$upload_url = Upload::inst()->update(APP_PATH . substr($src, 1));
				$data['art_content'] = str_replace($src, $upload_url, $data['art_content']);
				$data['art_md_content'] = str_replace($src, $upload_url, $data['art_md_content']);
			}
		}

		unset($data['upload_file']);
		if (empty($data['art_id'])) {
			unset($data['art_id']);
			$data['art_created_date'] = CK_NOW;
			$data['art_modified_date'] = CK_NOW;
			$flag = Article::inst()->insert($data, false);
		} else {
			$data['art_id'] = Cipher::inst()->decrypt($data['art_id']);
			if (!is_numeric($data['art_id'])) {
				return false;
			}
			$data['art_modified_date'] = CK_NOW;
			$flag = Article::inst()->update($data, ['art_id' => $data['art_id']]);
			return $flag;
		}
	}

	/**
	 * 获取一条文章详细信息
	 * @param $cipher_id
	 * @res true
	 *
	 * @return array|bool
	 */
	public function getOneArticle($cipher_id) {
		$art_id = Cipher::inst()->decrypt($cipher_id);
		if (!is_numeric($art_id)) {
			return false;
		}
		$info = Article::inst()->getArtById($art_id);
		return $info;
	}

	/**
	 * 逻辑删除一篇文章
	 * @res true
	 *
	 * @param $id
	 * @return bool
	 */
	public function delArticle($id) {
		if (empty($id)) {
			return false;
		}
		$res = Article::inst()->update(['is_del' => 1], ['art_id' => $id]);
		return $res;
	}

	/**
	 * 上传图片
	 * @param $base
	 * @res true
	 *
	 * @return bool|string
	 */
	public function uploadImg($base) {
		$file_path = Upload::inst()->updateBase64($base);
		return $file_path;
	}
}