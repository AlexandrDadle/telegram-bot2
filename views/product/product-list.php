<?php

/* @var $this \yii\web\View */
/* @var $products Products */


use app\models\Products;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$type = Products::getAllTypesOfProduct();
$form = ActiveForm::begin([
    'id' => 'create-product-form',
    'options' => ['class' => 'form-horizontal'],
])


?>
<?= $form->field($products, 'produkt_type')->dropDownList($type) ?>
<?= $form->field($products, 'produkt_name') ?>
<?= $form->field($products, 'cena_brutto') ?>
<?= $form->field($products, 'cena_netto') ?>
<?= $form->field($products, 'in_stock') ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
