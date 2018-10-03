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

    <?php foreach($model as $user) { ?>
        <div class="row">
            <div class="col-lg-12">
                <?php echo $user->username; ?>
<!--                --><?php //echo $user->last_payment; ?>
            </div>
        </div>
    <?php } ?>

</div>
