<?php

namespace ggss\push\model;

use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property string|null $title 消息标题
 * @property string|null $content 消息内容
 * @property int|null $send_id 发送者
 * @property string|null $receive_id 接收者
 * @property int $type 消息类型 0系统消息1关注2评论3点赞4收藏5提问6回答7转发
 * @property int $is_read 0未读1已读
 * @property string $target_table 目标表
 * @property int $target_id 目标id
 * @property int $created_at 创建时间
 * @property int|null $updated_at 更新时间
 * @property int|null $push_status 推送状态
 * @property int|null $deleted_at
 * @property int|null $is_deleted
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $push_type
 * @property BaseActiveRecord $target
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['send_id', 'type', 'is_read', 'target_id', 'created_at', 'updated_at', 'deleted_at', 'is_deleted', 'created_by', 'updated_by','push_type','push_status','push_timing_at'], 'integer'],
            [['title'], 'string', 'max' => 64],
            [['content','push_url'], 'string', 'max' => 255],
            [['receive_id'], 'integer'],
            [['target_table'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '消息标题',
            'content' => '消息内容',
            'send_id' => '发送者',
            'receive_id' => '接收者',
            'type' => '消息类型',
            'is_read' => '0未读1已读',
            'target_table' => '目标表',
            'target_id' => '目标id',
            'push_type' => '推送类型',
            'push_status' => '推送状态',
            'push_timing_at' => '定时推送时间',
            'push_url' => '推送链接',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => 'Deleted At',
            'is_deleted' => 'Is Deleted',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public static function firstOrFail($condition)
    {
        $one = static::findOne($condition);
        if (!$one) {
            throw new NotFoundHttpException('数据不存在');
        }
        return $one;
    }
}
