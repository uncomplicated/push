<?php

namespace ggss\push\model;

use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;
/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $device_no 设备号
 * @property string $ip IP
 * @property string $system 系统
 * @property string $version 版本号
 * @property int $created_at 创建时间
 * @property int|null $updated_at 更新时间
 * @property int|null $deleted_at
 * @property int|null $is_deleted
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'device_no', 'ip', 'system', 'version'], 'required'],
            [['uid', 'created_at', 'updated_at', 'deleted_at', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['device_no'], 'string', 'max' => 64],
            [['ip', 'system', 'version'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'device_no' => '设备号',
            'ip' => 'IP',
            'system' => '系统',
            'version' => '版本号',
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
