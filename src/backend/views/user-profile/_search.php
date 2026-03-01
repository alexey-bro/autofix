<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\search\UserProfileSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-profile-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fullName') ?>

    <?= $form->field($model, 'role') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'carBrand') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'reviewsCount') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'workAddress') ?>

    <?php // echo $form->field($model, 'firstName') ?>

    <?php // echo $form->field($model, 'lastName') ?>

    <?php // echo $form->field($model, 'experience') ?>

    <?php // echo $form->field($model, 'companyName') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
