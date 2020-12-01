<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M201029081014Message
 */
class M201029081014Message extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //消息通知表
        $this->createTable('message', [
            'id' => $this->primaryKey(10),
            'title' => $this->string(64)->null()->comment('消息标题'),
            'content' => $this->string(255)->defaultValue(0)->comment('消息内容'),
            'send_id' => $this->integer(11)->defaultValue(0)->comment('发送者'),
            'receive_id' => $this->integer(11)->null()->comment('接收者'),
            'type' => $this->tinyInteger(2)->notNull()->defaultValue(0)->comment('消息类型 0系统消息1关注2评论3点赞4空间送花5提醒打卡6申请入群7同意入群8退群9设置管理员10移交群主权限11解散小组12移除队员13移除管理员14拒绝入群15回复16视频送花'),
            'is_read' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('0未读1已读'),
            'target_table' => $this->string(20)->notNull()->comment('目标表'),
            'target_id' => $this->targetId()->notNull()->comment('目标id'),
            'push_type' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('推送类型 0不推送1单推2批量推3群推'),
            'push_status' => $this->tinyInteger(1) ->notNull()->defaultValue(0)->comment('推送状态 0未推送1已推送2推送中3推送失败'),
            ''
        ]);
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
