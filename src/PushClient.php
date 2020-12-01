<?php


namespace common\services;

use AppConditions;
use DictionaryAlertMsg;
use IGeTui;
use IGtAPNPayload;
use IGtAppMessage;
use IGtLinkTemplate;
use IGtNotificationTemplate;
use IGtNotify;
use IGtSingleMessage;
use IGtStartActivityTemplate;
use IGtTarget;
use IGtTransmissionTemplate;
use NotifyInfo_Type;
use RequestException;
use SimpleAlertMsg;
use Yii;
class PushClientService
{

    public $igt;
    private $logo_name;
    private $logo_url;
    private $offlineTime;
    private $host;
    private $appKey;
    private $masterSecret;
    private $appId;
    private $appSecret;
    public function __construct($config)
    {
        $this->_init($config);
        $this->igt = new IGeTui($this->host,$this->appKey, $this->masterSecret);
    }

    private function _init($config)
    {
        if(isset($config['host'])){
            $this->host = $config['host'];
        }else{
            $this->host = 'http://api.getui.com/apiex.htm';
        }
        if(isset($config['appKey'])){
            $this->appKey = $config['appKey'];
        }
        if(isset($config['masterSecret'])){
            $this->masterSecret = $config['masterSecret'];
        }
        if(isset($config['appId'])){
            $this->appId = $config['appId'];
        }
        if(isset($config['appSecret'])){
            $this->appSecret = $config['appSecret'];
        }
        if(isset($config['logo_name'])){
            $this->logo_name = $config['logo_name'];
        }
        if(isset($config['logo_url'])){
            $this->logo_url = $config['logo_url'];
        }
        if(isset($config['offlineTime'])){
            $this->offlineTime = $config['offlineTime'];
        }else{
            $this->offlineTime = 3600*12*1000;
        }
    }
    //单推
    public function pushMessageToSingle($cid ,$title , $content,$transparent_content='',$messageType = 1,$platform='android')
    {
        if($platform == 'android'){
            $template = $this->notificationTemplate($title , $content, $messageType,$transparent_content);
        }else{
            $template = $this->iosTemplate($title , $content,$transparent_content,$messageType);
        }

        $message = new IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime($this->offlineTime);//离线时间
        $message->set_data($template);//设置推送消息类型
        //接收方
        $target = new IGtTarget();
        $target->set_appId($this->appId);
        $target->set_clientId($cid);
        try {
            $rep = $this->igt->pushMessageToSingle($message, $target);
            if($rep['result'] == 'ok' && $rep['result'] = 'successed_online'){
                return true;
            }
            return isset($rep['result']) ? $rep['result'] : '';
        }catch(RequestException $e){
            $requstId =$e->getRequestId();
            $rep =  $this->igt->pushMessageToSingle($message, $target,$requstId);
            if($rep['result'] == 'ok' && $rep['result'] = 'successed_online'){
                return true;
            }
            return isset($rep['result']) ? $rep['result'] : '';
        }
    }
    //批量单推
    public function pushMessageToSingleBatch(array $cids ,$title , $content ){
        $batch = new IGtBatch($this->appKey , $this->igt);
        $batch->setApiUrl($this->host);
        foreach ( $cids as $cid){
            $templateNoti = $this->notificationTemplate($title , $content);
            $message = new IGtSingleMessage();
            $message->set_isOffline(true);//是否离线
            $message->set_offlineExpireTime($this->offlineTime);//离线时间
            $message->set_data($templateNoti);//设置推送消息类型
            $target = new IGtTarget();
            $target->set_appId($this->appId);
            $target->set_clientId($cid);
            $batch->add($message, $target);
        }
        try {
            $rep = $batch->submit();
            if($rep['result'] == 'ok' && $rep['result'] = 'successed_online'){
                return true;
            }
            return isset($rep['result']) ? $rep['result'] : '';
        }catch(Exception $e){
            $rep=$batch->retry();
            if($rep['result'] == 'ok' && $rep['result'] = 'successed_online'){
                return true;
            }
            return isset($rep['result']) ? $rep['result'] : '';
        }
    }
    //群推  $condition  ['phoneType' => 'ios','region'=>'上海']
    public function pushMessageToApp($content, $timing='' , $speed='' , $condition = [],$transparent_content='',$messageType = 1,$platform='android')
    {
        if($platform == 'android'){
            $template = $this->notificationTemplate('',$content,$messageType,$transparent_content);
        }else{
            $template = $this->iosTemplate('' , $content,$transparent_content,$messageType);
        }
        $message = new IGtAppMessage();
        $message->set_isOffline(true);
        if(!empty($timing)){
            $message->setPushTime($timing);//在用户设定的时间点进行推送，格式为年月日时分
        }
        if(!empty($speed)){
            $message->set_speed($speed);//定速推送,设置setSpeed为100，则全量送时个推控制下发速度在100条/秒左右。
        }

        $message->set_offlineExpireTime($this->offlineTime);
        $message->set_data($template);
        $appIdList=array($this->appId);//支持多个app群发
        $message->set_appIdList($appIdList);
        $cdt = $this->_condition($condition);
        if(!empty($cdt)){
            $message->set_conditions($cdt);
        }

        $rep = $this->igt->pushMessageToApp($message);
        if($rep['result'] == 'ok' && $rep['result'] = 'successed_online'){
            return true;
        }
        return isset($rep['result']) ? $rep['result'] : '';
    }
    //组合群推条件
    private function _condition(array $condition)
    {
        if(empty($condition)) return '';
        $cdt = new AppConditions();
        foreach ($condition as $key => $value){
            $data=array($value); //根据手机类型群发
            $cdt->addCondition3($key, $data);
        }
        return $cdt;
    }
    //自定义消息模板
    private function transmissionTemplate($title,$content ,$messageType = 2)
    {
        $template =  new IGtTransmissionTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_transmissionType($messageType);//透传消息类型
        $template->set_transmissionContent($content);//透传内容
        $apn = new IGtAPNPayload();
        $alertmsg=new SimpleAlertMsg();
        $alertmsg->alertMsg="";
        $apn->alertMsg=$alertmsg;
        $apn->badge=2;
        $apn->sound="";
        $apn->add_customMsg("payload","payload");
        $apn->contentAvailable=1;
        $apn->category="ACTIONABLE";
        $template->set_apnInfo($apn);
        //第三方厂商推送透传消息带通知处理
        $notify = new IGtNotify();
        $notify -> set_payload("{}");
        $notify -> set_title($title);
        $notify -> set_content($content);
        $notify->set_type(NotifyInfo_Type::_payload);
        $template -> set3rdNotifyInfo($notify);
        return $template;
    }
    //打开应用首页模板
    private function notificationTemplate($title,$content ,$messageType = 2 ,$transparent_content='')
    {
        $template =  new IGtNotificationTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_transmissionType($messageType);               //透传消息类型
        $template->set_transmissionContent($transparent_content);   //透传内容(用户无感知)
        $template->set_title($title);                     //通知栏标题
        $template->set_text($content);        //通知栏内容
        $template->set_logo($this->logo_name);                  //通知栏logo
        $template->set_logoURL($this->logo_url); //通知栏logo链接
        $template->set_isRing(true);                      //是否响铃
        $template->set_isVibrate(true);                   //是否震动
        $template->set_isClearable(true);                 //通知栏是否可清除
        return $template;
    }
    //打开浏览器网页
    private function linkTemplate($title,$content , $url)
    {
        $template =  new IGtLinkTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo($this->logo_name);//通知栏logo
        $template->set_logoURL($this->logo_url);
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        $template ->set_url($url); //打开连接地址
        return $template;
    }
    //打开应用内页面
    private function activityTemplate($title,$content , $url)
    {
        $template = new IGtStartActivityTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_intent($this->addIntent($url));
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo($this->logo_name);//通知栏logo
        $template->set_logoURL($this->logo_url);
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        return $template;
    }
    //生成intent
    private function addIntent($url)
    {
        //todo
    }

    //ios推送
    private function iosTemplate($title,$content,$transparent_content='',$messageType = 1)
    {
        $template =  new IGtTransmissionTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_transmissionType($messageType);//透传消息类型
        $apn = new IGtAPNPayload();
        $alertmsg=new DictionaryAlertMsg();
        $alertmsg->body=$content;
        $alertmsg->title = $title;
        $alertmsg->subtitleLocArgs = [];
        $apn->alertMsg=$alertmsg;
        $apn->add_customMsg("payload",$transparent_content);
        $template->set_apnInfo($apn);
        return $template;
    }
}