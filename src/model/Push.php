<?php

namespace ggss\push\model;

use Yii;

/**
 * This is the model class for table "push".
 *
 * @property int $id
 * @property string|null $title 推送标题
 * @property string|null $content 推送内容
 * @property string|null $device_no 设备号
 * @property string|null $system 手机系统
 * @property int $push_type 推送类型 1单推2批量推3群推
 * @property int $push_status 推送状态 0未推送1已推送2推送中3推送失败
 * @property int $push_timing_at 定时推送时间
 * @property string|null $push_url 推送链接
 * @property int $created_at 创建时间
 * @property int|null $updated_at 更新时间
 * @property int|null $deleted_at
 * @property int|null $is_deleted
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Push extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'push';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['push_type', 'push_status', 'push_timing_at', 'created_at', 'updated_at', 'deleted_at', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['created_at'], 'required'],
            [['title', 'device_no','system'], 'string', 'max' => 64],
            [['content', 'push_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '推送标题',
            'content' => '推送内容',
            'device_no' => '设备号',
            'system' => '手机系统',
            'push_type' => '推送类型 1单推2批量推3群推',
            'push_status' => '推送状态 0未推送1已推送2推送中3推送失败',
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

}
