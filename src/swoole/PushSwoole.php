<?php
/**
 * User: xujia
 * Date: 2020/12/8
 */

namespace ggss\push\swoole;


use ggss\push\enums\MessageEnum;
use ggss\push\model\Device;
use ggss\push\model\Message;
use ggss\push\PushClient;
use Swoole\Process\Pool;
use Yii;

class PushSwoole
{

    public function run($config)
    {
        $workerNum = 2;
        $pool = new Pool($workerNum);

        $pool->on('WorkerStart', function ($pool, $workerId) use($config){
            $model = Message::find()->andWhere(['is_deleted' => 0,'push_status'=>MessageEnum::MESSAGE_PUSH_STATUS_DEFAULT])->orderBy(['id' => SORT_DESC])->one();
            if (empty($model)) {
                return;
            }
            usleep(rand(10,999));
            if (!Yii::$app->mutex->acquire('lock_' . $model->id)) {
                return;
            }
            if($model->push_type == MessageEnum::MESSAGE_PUSH_TYPE_SINGLE && $model->send_id == $model->receive_id){
                return;
            }
            $this->updateMessageStatus($model,MessageEnum::MESSAGE_PUSH_STATUS_ONGOING);
            //判断是否推送
            if($model->push_type == MessageEnum::MESSAGE_PUSH_TYPE_UNWANTED){
                $this->updateMessageStatus($model,MessageEnum::MESSAGE_PUSH_STATUS_SUCCESS);
                return false;
            }
            $pushModel = new PushClient($config);
            switch ($model->push_type){
                case MessageEnum::MESSAGE_PUSH_TYPE_SINGLE :
                case MessageEnum::MESSAGE_PUSH_TYPE_BATCH:
                    $deviceModel = Device::find()->where(['is_deleted' => 0,'uid' =>$model->receive_id ])->one();
                    if(empty($deviceModel) || empty($deviceModel->device_no) || (empty($model->title) && empty($model->content)) ){//数据错误
                        Yii::error('推送失败id为：'.$this->id.'设备号为空,或者标题和内容同时为空','push');
                        $this->updateMessageStatus($model,MessageEnum::MESSAGE_PUSH_STATUS_ERROR);
                        return false;
                    }
                    $res = $pushModel->pushMessageToSingle($deviceModel->device_no,$model->title,$model->content,json_encode(['route' => $model->push_url]),1,$deviceModel->system);
                    break;
                case MessageEnum::MESSAGE_PUSH_TYPE_TO_APP:
                    //判断是否定时发送
                    if(!empty($model->push_timing_at) && $model->push_timing_at > time()){
                        return false;
                    }
                    if(empty($model->push_timing_at) && isset($config['overtime']) &&  time() - $model->created_at < $config['overtime'] ){
                        return false;
                    }
                    $res = $pushModel->pushMessageToApp($model->title,$model->content,'','',[],json_encode(['route' =>$model->push_url]));
                    $res = $pushModel->pushMessageToApp($model->title,$model->content,'','',[],json_encode(['route' =>$model->push_url]),1,'ios');
                    break;
            }
            if(isset($res) && is_string($res)){
                $this->updateMessageStatus($model,MessageEnum::MESSAGE_PUSH_STATUS_ERROR);
                Yii::error('推送失败id为：'.$this->id.', 推送类型:'.$model->push_type.' 错误信息：'.$res,'push');
                return false;
            }else{
                $this->updateMessageStatus($model,MessageEnum::MESSAGE_PUSH_STATUS_SUCCESS);
            }

            Yii::$app->mutex->release('lock_' . $model->id);
        });

        $pool->on('WorkerStop', function ($pool, $workerId) {

        });

        $pool->start();
    }

    private function updateMessageStatus(Message $model, $status){
        $model->push_status = $status;
        if($model->save()){
            return true;
        }
        return false;
    }
}