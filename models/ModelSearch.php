<?php

namespace namwansoft\LogsBehavior\models;

class ModelSearch extends Model
{

    public $q;

    public function rules()
    {
        return [[['q'], 'safe']];
    }

    public function scenarios()
    {
        return \yii\base\Model::scenarios();
    }

    public function search($params)
    {
        $query = get_class($this)::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->q['event']) {
            $query->andFilterWhere(['like', 'event', $this->q['event']]);
        }
        if ($this->q['user']) {
            $query->andFilterWhere(['user' => $this->q['user']]);
        }
        $query->andFilterWhere(['OR',
            ['like', 'change_attributes', $this->q['search']],
            ['like', 'object', $this->q['search']],
        ]);

        return $dataProvider;
    }

}
