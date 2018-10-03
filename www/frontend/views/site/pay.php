<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\Payment */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use janisto\timepicker\TimePicker;

$this->title = 'Pay';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-pay">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Now you can send some your money to another user.
    </p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'pay-form']); ?>

            <?= $form->field($model, 'id_user_from')->textInput(['value'=>$user->id, 'disabled' => true]) ?>

            <?= $form->field($model, 'id_user_to')->dropDownList($users,[ 'prompt' => 'Choose please receiver' ]) ?>

            <?= $form->field($model, 'amount') ?>

            <?= $form->field($model, 'deferred_time')->widget(TimePicker::className(), [
                'language' => 'en',
                'mode' => 'datetime',
                'clientOptions'=>[
                    'dateFormat' => 'dd.mm.yy',
                    'timeFormat' => 'H:00',
                    'hourGrid' => 6,
                    'minDate'=>  'today',
                    'minTime'=>  'now',
                ]
            ]);?>

            <div class="form-group">
                <?= Html::submitButton('Send money', ['class' => 'btn btn-primary', 'name' => 'pay-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
