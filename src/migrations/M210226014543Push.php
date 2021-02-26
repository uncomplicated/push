<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M210226014543Push
 */
class M210226014543Push extends Migration
{
    /**
     * {@inheritdoc}
     */
    const DEFAULT_OPTION = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    public function safeUp()
    {
        //消息通知表
        $this->createTable('push', [
            'id' => $this->primaryKey(10),
            'title' => $this->string(64)->null()->comment('推送标题'),
            'content' => $this->string(255)->null()->comment('推送内容'),
            'device_no' => $this->string(64)->comment('设备号'),
            'system' => $this->string(64)->comment('手机系统'),
            'push_type' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('推送类型 1单推2批量推3群推'),
            'push_status' => $this->tinyInteger(1) ->notNull()->defaultValue(0)->comment('推送状态 0未推送1已推送2推送中3推送失败'),
            'push_timing_at' => $this->integer(10)->notNull()->defaultValue(0)->comment('定时推送时间'),
            'push_url' => $this->string(255)->null()->comment('推送链接'),
            'created_at' => $this->integer(10)->notNull()->comment('创建时间'),
            'updated_at' => $this->integer(10)->null()->comment('更新时间'),
            'deleted_at' => $this->integer(10)->null(),
            'is_deleted' => $this->boolean()->defaultValue(0),
            'created_by' => $this->integer(10)->null(),
            'updated_by' => $this->integer(10)->null(),
        ],self::DEFAULT_OPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M210226014543Push cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M210226014543Push cannot be reverted.\n";

        return false;
    }
    */
}
