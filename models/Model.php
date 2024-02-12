<?php

namespace namwansoft\LogsBehavior\models;

use Yii;

class Model extends \yii\db\ActiveRecord
{

    private static $db = 'db';
    private static $table = 'app_log_model_behavior';

    public static function getDb()
    {
        return Yii::$app->get(self::$db);
    }

    public static function tableName()
    {
        return '{{%' . self::$table . '}}';
    }

    public function rules()
    {
        return [
            [['change_attributes'], 'string'],
            [['object_id', 'user', 'created_at'], 'integer'],
            [['event', 'object'], 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'change_attributes' => 'Attributes',
            'user'              => 'User',
            'event'             => 'Event',
            'object_id'         => 'Object Id',
            'object'            => 'Object',
            'created_at'        => 'CreatedAt',
        ];
    }

    public function getUsers()
    {
        return $this->hasOne(\common\models\AuthUser::className(), ['id' => 'user']);
    }

    public function behaviors()
    {
        return [
            [
                'class'              => \yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ],
        ];
    }

    public function beforeSave($acttion)
    {
        $this->object = str_replace("common\\models\\", "", $this->object);
        $this->user = Yii::$app->user->id ?? null;
        return parent::beforeSave($acttion);
    }

    public function afterFind()
    {
        foreach ($this->attributes as $key => $value) {
            if (is_object(json_decode($value))) {
                $this->$key = json_decode($value);
            }
        }
        parent::afterFind();
    }
}
