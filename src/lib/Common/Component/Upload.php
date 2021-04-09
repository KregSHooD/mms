<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/9/18
 * Time: 14:48
 */

namespace Common\Component;


use CK\Core\Component;
use CK\Util\ImageUpload;
use CK\Util\IO\File;
use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 上传组件
 * Class Upload
 *
 * @package App\Manage\Component
 */
class Upload extends Component{
    //组件配置
    private $cfg;
    //OSS客户端
    private $oss;
    //OSS KEY ID
    private $access_key_id;
    //OSS KEY SECRET
    private $access_key_secret;
    //OSS 访问域名
    private $domain;
    //OOS 是否自定义域名
    private $is_custom_domain;
    //OSS 访问的 bucket
    private $bucket;
    //OSS 上传目录
    private $dir_path;

    public function __construct() {
        //得到组件配置
        $this->cfg = $this->getConfig();
        $this->access_key_id = $this->cfg['access_key_id'];
        $this->access_key_secret = $this->cfg['access_key_secret'];
        $this->domain = $this->cfg['domain'];
        $this->is_custom_domain = $this->cfg['is_custom'];
        $this->bucket = $this->cfg['bucket'];
        $this->dir_path = $this->cfg['dir_path'];

        $this->oss = new OssClient($this->access_key_id,
            $this->access_key_secret,
            $this->domain,
            $this->is_custom_domain);

    }

    public function update($local_file) {
        $file_name = basename($local_file);
        try {
            $this->oss->uploadFile($this->bucket, $this->dir_path.$file_name,$local_file);
            File::Delete($local_file);
        } catch(OssException $e) {
            throw $e;
        }
        return $this->domain.'/'.$this->dir_path.$file_name;
    }

    public function updateBase64($base) {
        $path = ImageUpload::inst()->saveFromBase64($base,['image_type'=>'default']);
        return $path;
    }
}