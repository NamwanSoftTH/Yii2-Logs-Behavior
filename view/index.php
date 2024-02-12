<?php

    use Yii;
    use yii\bootstrap5\Modal;
    use yii\widgets\Pjax;

    $this->title = Yii::t('app', 'Logs Behavior') . ' : ' . Yii::$app->name;

    Modal::begin();
    Modal::end();
    $this->registerJsFile('@themesAsset/js/form-all.js' . CDNV, ['depends' => [yii\web\JqueryAsset::className()]]);

    $this->registerJsFile(CDNH . '/assets/plugins/custom/jstree/jstree.bundle.js' . CDNV, ['depends' => [yii\web\JqueryAsset::className()]]);
    $this->registerCssFile(CDNH . '/assets/plugins/custom/jstree/jstree.bundle.css' . CDNV, ['depends' => [yii\web\JqueryAsset::className()]]);

    $arType = [
        'afterInsert'  => ['event' => 'Insert', 'case' => 1, 'text' => '<span class="text-primary"><i class="text-primary fa fa-plus"></i> Insert</span>'],
        'afterUpdate'  => ['event' => 'Update', 'case' => 2, 'text' => '<span class="text-info"><i class="text-info fa fa-edit"></i> Update</span>'],
        'beforeDelete' => ['event' => 'Delete', 'case' => 0, 'text' => '<span class="text-danger"><i class="text-danger fa fa-trash"></i> Delete</span>'],
    ];
    $arType_L = \yii\helpers\ArrayHelper::map($arType, 'event', 'event');
    function subOne($k, $v1, $case = 0)
    {
        if (!$v1) {return;}
        $res = '';
        if (is_object($v1)) {
            foreach ($v1 as $key => $value) {
                $res .= subOne($key, $value);
            }
        } else {
            $res .= '<li data-jstree=\'{ "type" : "file" }\'><span class="badge badge-light-success mx-1">' . $k . '</span>' . ((!$case) ? '<span class="badge badge-light-info mx-1">' . $v1 . '</span>' : '<span class="badge badge-light-primary mx-1">' . $v1 . '</span>') . '</li>';
        }
        return $res;
    }

    function subBF($k, $v1, $v2)
    {
        $res = '';
        // $v1 = (is_array($v1)) ? (object) $v1 : $v1;
        // $v2 = (is_array($v2)) ? (object) $v2 : $v2;
        if ((is_object($v1) || is_object($v2))) {
            $arKey = array_unique(array_merge(array_keys((array) $v1), array_keys((array) $v2)));
            foreach ($arKey as $key => $value) {
                if (is_object($v1->{$value}) || is_object($v2->{$value})) {
                    $res .= subBF($value, $v1->{$value}, $v2->{$value});
                } else if ($v1->{$value} !== $v2->{$value}) {
                    $res .= '<li data-jstree=\'{ "type" : "file" }\'><span class="badge badge-light-success">' . $value . '</span><span class="badge badge-light-info">' . $v1->{$value} . '</span><span class="badge badge-light-primary">' . $v2->{$value} . '</span></li>';
                }
            }
        } else if ($v1 != $v2) {
            $res .= '<li data-jstree=\'{ "type" : "file" }\'><span class="badge badge-light-success mx-1">' . $k . '</span><span class="badge badge-light-info mx-1">' . $v1 . '</span><span class="badge badge-light-primary mx-1">' . $v2 . '</span></li>';
        }
        return $res;
    }
?>
<script>
function jstreeSet() {
    $(".jsTree").jstree({
        "core": {
            "themes": {
                "responsive": false
            }
        },
        "types": {
            "default": {
                "icon": "fad fa-folder",
            },
            "file": {
                "icon": "fad fa-file-edit"
            }
        },
        "plugins": ["types"]
    });
}
</script>
<?php Pjax::begin(['id' => 'grid_pjax', 'timeout' => false]);?>
<div class="card h-md-100">
    <div class="card-header px-3">
        <h3 class="card-title"><i class="fad fa-file-medical-alt"></i>&nbsp;<?=Yii::t('app', 'Logs Behavior');?></h3>
        <div class="card-toolbar">
            <?=$this->render('_search', ['model' => $searchModel, 'arType_L' => $arType_L]);?>
        </div>
    </div>
    <div class="card-body p-3">
        <?=
\yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    // 'rowOptions'   => function ($model) {
    //     if ($model->channel->company->id !== \Yii::$app->Company->id) {
    //         return ['class' => 'text-inverse-dark bg-dark'];
    //     }
    //     $class = null;
    //     if ($model->customer_id) {
    //         $class = 'success';
    //     }
    //     return ($model->void_all_flag == 1) ? ['class' => 'danger'] : ['class' => $class];
    // },
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'      => 'event',
            'format'         => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'options'        => ['style' => 'min-width:100px;width:100px;'],
            'value'          => function ($model) use ($arType) {
                return $arType[$model->event]['text'];
            },
        ],
        [
            'attribute'      => 'object',
            'contentOptions' => ['class' => 'text-center'],
            'options'        => ['style' => 'min-width:150px;width:150px;'],
        ],
        [
            'attribute'      => 'object_id',
            'contentOptions' => ['class' => 'text-center'],
            'options'        => ['style' => 'min-width:100px;width:100px;'],
        ],
        [
            'attribute' => 'change_attributes',
            'format'    => 'raw',
            // 'contentOptions' => ['class' => 'text-center'],
            'options'   => ['style' => 'min-width:150px;'],
            'value'     => function ($model) use ($arType) {
                $Txt = '';
                switch ($arType[$model->event]['event']) {
                case 'Insert':
                case 'Delete':
                    $Txt = '<div class="jsTree"><ul>';
                    foreach ($model->change_attributes as $k => $v) {
                        if (is_object(json_decode($v))) {
                            $v = json_decode($v);
                            if ($Res = subOne($model->getAttributeLabel($k), $v, $arType[$model->event]['case'])) {
                                $Txt .= '<li data-jstree=\'{ "opened" : true }\'><span class="badge badge-light-success mx-1">' . $model->getAttributeLabel($k) . '</span><ul>' . $Res . '</ul></li>';
                            }
                        } else {
                            $Txt .= subOne($model->getAttributeLabel($k), $v, $arType[$model->event]['case']);
                        }
                    }
                    $Txt .= '</ul></div>';
                    break;
                case 'Update':
                    $Txt = '<div class="jsTree"><ul>';
                    foreach ($model->change_attributes as $k => $v) {
                        if (is_object(json_decode($v[0])) || is_object(json_decode($v[1]))) {
                            $v = [json_decode($v[0]), json_decode($v[1])];
                            if ($Res = subBF($model->getAttributeLabel($k), $v[0], $v[1])) {
                                $Txt .= '<li data-jstree=\'{ "opened" : true }\'><span class="badge badge-light-success mx-1">' . $model->getAttributeLabel($k) . '</span><ul>' . $Res . '</ul></li>';
                            }
                        } else {
                            $Txt .= subBF($model->getAttributeLabel($k), $v[0], $v[1]);
                        }
                    }
                    $Txt .= '</ul></div>';
                    break;
                default:
                    break;
                }

                return $Txt;
            },
        ],
        [
            'attribute'      => 'user',
            'contentOptions' => ['class' => 'text-center'],
            'options'        => ['style' => 'min-width:100px;width:100px;'],
            'value'          => function ($model) {
                return $model->users->name;
            },
        ],
        [
            'attribute'      => 'created_at',
            'format'         => 'datetime',
            'contentOptions' => ['class' => 'text-center'],
            'options'        => ['style' => 'min-width:150px;width:150px;'],
        ],
    ],
]);
?>
    </div>
</div>
<?php Pjax::end();?>
<?php
$this->registerJs('
jstreeSet();
$("body").on("pjax:end", e => {
    jstreeSet();
});
');
?>