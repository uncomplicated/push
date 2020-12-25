<?php

namespace ggss\push\src\migrations;

use yii\db\Migration;

/**
 * Class M201029081014Message
 */
class M201029081014Message extends Migration
{
    /**
     * {@inheritdoc}
     */
    const DEFAULT_OPTION = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    public function safeUp()
    {
        //消息通知表
        $this->createTable('message', [
            'id' => $this->primaryKey(10),
            'title' => $this->string(64)->null()->comment('消息标题'),
            'content' => $this->string(255)->null()->comment('消息内容'),
            'send_id' => $this->integer(11)->defaultValue(0)->comment('发送者'),
            'receive_id' => $this->integer(11)->null()->comment('接收者'),
            'type' => $this->tinyInteger(2)->notNull()->defaultValue(0)->comment('消息类型'),
            'is_read' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('0未读1已读'),
            'target_table' => $this->string(20)->null()->comment('目标表'),
            'target_id' => $this->integer(10)->null()->comment('目标id'),
            'push_type' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('推送类型 0不推送1单推2批量推3群推'),
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

        $this->createTable('device',[
            'id' => $this->primaryKey(),
            'uid' => $this->integer(11)->notNull()->comment('用户ID'),
            'device_no' => $this->string(64)->null()->comment('设备号'),
            'ip' => $this->string(32)->notNull()->comment('IP'),
            'system' => $this->string(32)->notNull()->comment('系统'),
            'version' => $this->string(32)->notNull()->comment('版本号'),
            'created_at' => $this->integer(10)->notNull()->comment('创建时间'),
            'updated_at' => $this->integer(10)->null()->comment('更新时间'),
            'deleted_at' => $this->integer(10)->null(),
            'is_deleted' => $this->boolean()->defaultValue(0),
            'created_by' => $this->integer(10)->null(),
            'updated_by' => $this->integer(10)->null(),
        ],self::DEFAULT_OPTION);
        $this->createIndex('idx_uid','device','uid');
        $this->createIndex('idx_device_no','device','device_no');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M201029081014Message cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M201029081014Message cannot be reverted.\n";

        return false;
    }
    */
}
