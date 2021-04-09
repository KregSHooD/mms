<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/10/29
 * Time: 20:49
 */

namespace Common\Component;


use CK\Core\Component;
use CK\Util\Http;
use CK\Util\Log;

class WxCard extends Component{

    /**
     * 创建卡券
     * @param $base_info
     *
     * @return bool
     * @throws \Exception
     */
    public function createCard($base_info) {
        $url = 'https://api.weixin.qq.com/card/create?access_token='.Platform::inst()->getAccessToken();
        $res = $this->http($url, $base_info);
        if ($res['errcode'] === 0) {
            return $res['card_id'];
        } else {
            throw new \Exception($res['errmsg']);
            return false;
        }
    }

    /**
     * 更新会议卡券信息
     * @param $update
     *
     * @return bool
     * @throws \Exception
     */
    public function updateMeeting($update) {
        $url = 'https://api.weixin.qq.com/card/meetingticket/updateuser?access_token='
            .Platform::inst()->getAccessToken();
        $res = $this->http($url, $update);
        if ($res['errcode'] === 0) {
            return true;
        } else {
            throw new \Exception($res['errmsg']);
            return false;
        }
    }

    /**
     * 得到卡券信息
     * @param $card_id
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function getCardInfo($card_id) {
        $url = 'https://api.weixin.qq.com/card/get?access_token='.Platform::inst()->getAccessToken();
        $data = [
            'card_id'=>$card_id
        ];
        $res = $this->http($url, $data);
        if ($res['errcode'] === 0) {
            return $res;
        } else {
            throw new \Exception($res['errmsg']);
            return false;
        }
    }

    /**
     * 核销卡券
     * @param $code
     * @param $card_id
     *
     * @return bool
     * @throws \Exception
     */
    public function consume($code,$card_id) {
        $url = 'https://api.weixin.qq.com/card/code/consume?access_token='
            .Platform::inst()->getAccessToken();
        $data = [
            'code'=>$code,
            'card_id'=>$card_id
        ];
        $res = $this->http($url, $data);
        if ($res['errcode'] === 0) {
            return true;
        } else {
            Log::inst()->access($res['errmsg']);
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