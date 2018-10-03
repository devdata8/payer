<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\User */

use yii\helpers\Html;

$this->title = 'Payers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-payers">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-2">
            User:
        </div>
        <div class="col-lg-2">
            Last payment Id:
        </div>
        <div class="col-lg-2">
            Last payment Amount:
        </div>
    </div>
    <?php foreach($model as $user) { ?>
        <div class="row">
            <div class="col-lg-2">
                <?= $user['username'] ?> (id: <?= $user['uid'] ?>)
            </div>
            <div class="col-lg-2">
                <?php echo !empty($user['id'])?$user['id']:"-"; ?>
            </div>
            <div class="col-lg-2">
                <?= $user['amount'] ?>
            </div>
        </div>
    <?php } ?>

</div>
