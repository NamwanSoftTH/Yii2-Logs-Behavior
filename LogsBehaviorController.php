<?php

namespace namwansoft\LogsBehavior;

use namwansoft\LogsBehavior\models\ModelSearch;
use Yii;

class LogsBehaviorController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new ModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('@vendor/namwansoft/yii2-logs-behavior/view/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
