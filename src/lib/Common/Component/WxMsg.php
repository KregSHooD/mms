<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/11/1
 * Time: 21:21
 */

namespace Common\Component;


use CK\Core\Component;
use CK\Util\Http;
use CK\Util\Log;

class WxMsg extends Component{

    /**
     * 发送微信模板消息
     * @param        $open_id
     * @param        $tpl_id
     * @param        $msg
     * @param string $url
     *
     * @return bool
     */
    public function sendTemplateMessage($open_id,$tpl_id,$msg,$outurl='') {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='
            .Platform::inst()->getAccessToken();
        $data = [
            'touser'=>$open_id,
            'template_id'=>$tpl_id,
            'url'=>$outurl,
            'data'=>$msg
        ];

        $res = $this->http($url, $data);
        if ($res && $res['errcode'] === 0) {
            return true;
        } else {
            Log::inst()->access('微信模板消息调用出错',$res);
            return false;
        }
    }

    private function http($url,$data) {
        $res = Http::post_json($url, json_encode($data,JSON_UNESCAPED_UNICODE));
        if ($res) {
            $res_data = json_decode($res['content'],true);
            return $res_data;
        } else {
            return false;
        }
    }
}