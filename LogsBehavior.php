<?php

namespace namwansoft\LogsBehavior;

use namwansoft\LogsBehavior\models\Model;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class LogsBehavior extends \yii\base\Behavior
{

    public $exAttr = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'SaveLog',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'UpdateLog',
            ActiveRecord::EVENT_BEFORE_DELETE => 'SaveLog',
        ];
    }
    public function setChangelogLabels(array $diff)
    {
        return $diff;
    }

    public function SaveLog(Event $event)
    {
        $modelL = $event->sender;
        $diff = $modelL->attributes;
        $diff = $this->applyExclude($diff);
        $model = new Model();
        $model->change_attributes = Json::encode($diff);
        $model->event = $event->name;
        $model->object = $modelL::className();
        $model->object_id = $this->getPK($modelL);
        $model->save();
    }

    public function UpdateLog(Event $event)
    {
        $owner = $this->owner;
        $diff = [];
        foreach ($event->changedAttributes as $attrName => $attrVal) {
            $newAttrVal = $owner->getAttribute($attrName);
            if ($newAttrVal != $attrVal) {
                $diff[$attrName] = (object) [$attrVal, $newAttrVal];
            }
        }
        $diff = $this->applyExclude($diff);
        if ($diff) {
            $modelL = $event->sender;
            $model = new Model();
            $model->change_attributes = Json::encode((object) $diff);
            $model->event = $event->name;
            $model->object = $modelL::className();
            $model->object_id = $this->getPK($modelL);
            $model->save();
        }
    }

    private function getPK($model)
    {
        $pks = $model::getTableSchema()->primaryKey;
        return (count($pks) === 1) ? $model->{$pks[0]} : null;
    }

    private function applyExclude(array $diff)
    {
        foreach ($this->exAttr as $attr) {
            unset($diff[$attr]);
        }
        unset($diff['created_at']);
        unset($diff['updated_at']);
        return $diff;
    }
}
