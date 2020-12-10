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

    public function actionRun()
    {
        $workerNum = 2;
        $pool = new Pool($workerNum);

        $pool->on('WorkerStart', function ($pool, $workerId){
            echo "worker#{$workerId} is Started \n";
            $model = Message::find()->notDeleted()->andWhere(['push_status'=>MessageEnum::MESSAGE_PUSH_STATUS_DEFAULT])->orderBy(['id' => SORT_DESC])->one();
            if (empty($model)) {
                return;
            }
            if (!Yii::$app->mutex->acquire('lock_' . $model->id, 3)) {
                return;
            }
            $pushModel = new PushClient(Yii::$app->params['push']);
            switch ($model->push_type){
                case MessageEnum::MESSAGE_PUSH_TYPE_SINGLE :
                case MessageEnum::MESSAGE_PUSH_TYPE_BATCH:
                    $deviceModel = Device::find()->byUid($model->receive_id)->notDeleted()->one();
                    if(empty($deviceModel) || empty($deviceModel->device_no) || (empty($model->title) && empty($model->content))){
                        return false;
                    }
                    $res = $pushModel->pushMessageToSingle($deviceModel->device_no,$model->title,$model->content,json_encode(['route' => MessageEnum::$urls[$model->type]]));
                    break;
                case MessageEnum::MESSAGE_PUSH_TYPE_TO_APP:
                    $res = $pushModel->pushMessageToApp($model->content);
                    break;
            }
            if(isset($res['result'])){
                Yii::error('推送失败id为：'.$this->id.', 推送类型:'.$model->push_type.' 错误信息：'.$res['result'],'push');
                return false;
            }

            Yii::$app->mutex->release('lock_' . $model->id);
        });

        $pool->on('WorkerStop', function ($pool, $workerId) {
            echo "Worker#{$workerId} is stopped\n";
        });

        $pool->start();
    }

}