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
        $query = get_class($this)::find()->alias('tbM')->joinWith('users users');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['tbM.id' => SORT_DESC]],
            'pagination' => [],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->q['event']) {
            $query->andFilterWhere(['like', 'tbM.event', $this->q['event']]);
        }
        if ($this->q['user']) {
            $query->andFilterWhere(['like', 'users.name', $this->q['user']]);
        }
        $query->andFilterWhere(['OR',
            ['like', 'tbM.change_attributes', $this->q['search']],
            ['like', 'tbM.object', $this->q['search']],
        ]);

        return $dataProvider;
    }

}
