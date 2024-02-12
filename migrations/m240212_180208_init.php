<?php

class m240212_180208_init extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%app_log_model_behavior}}', [
            'id'                => $this->primaryKey(),
            'change_attributes' => $this->text(),
            'user'              => $this->integer(),
            'event'             => $this->string(30),
            'object_id'         => $this->integer(),
            'object'            => $this->string(30),
            'created_at'        => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        echo "m240212_180208_init cannot be reverted.\n";
        return false;
    }
}
