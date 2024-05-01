<?php

    $allUser = \yii\helpers\ArrayHelper::map(\common\models\AuthUser::findAll(), 'id', 'name');
    $form = \yii\widgets\ActiveForm::begin([
        'action'  => ['index'],
        'method'  => 'get',
        'options' => ['data-pjax' => true],
    ]);
?>
<div class="input-group input-group-sm">
    <?=\yii\helpers\Html::activeDropDownList($model, 'q[event]', ['' => ''] + $arType_L, [
    'class'            => "form-select form-select-sm rounded-end-0",
    'data-control'     => "select2",
    'data-placeholder' => "-" . Yii::t('app', 'Type') . "-",
    'data-allow-clear' => "true",
    'data-hide-search' => "true",
    'data-width'       => "150px",
]);?>
    <?=\yii\helpers\Html::activeDropDownList($model, 'q[user]', ['' => ''] + $allUser, [
    'class'            => "form-select form-select-sm rounded-end-0",
    'data-control'     => "select2",
    'data-placeholder' => "-" . Yii::t('app', 'Type') . "-",
    'data-allow-clear' => "true",
    'data-hide-search' => "true",
    'data-width'       => "150px",
]);?>
    <?=\yii\helpers\Html::activeTextInput($model, 'q[search]', ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Search') . '...']);?>
    <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> <?=Yii::t('app', 'Search');?></button>
    <!-- <?=\yii\helpers\Html::button('<i class="fad fa-repeat-1-alt"></i> ' . Yii::t('app', 'Reload'), ['class' => 'btn btn-dark actLoad']);?> -->
</div>
<?php $form::end();?>