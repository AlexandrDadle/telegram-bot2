<?php



/* @var $this \yii\web\View */
/* @var $dataprovider \yii\data\ActiveDataProvider */
?>
<?php echo \yii\grid\GridView::widget([
    'dataProvider' => $dataprovider,
    'columns' => [
        'produkt_type',
        'produkt_name',
        'cena_brutto',
        'cena_netto',
        'in_stock',
        [
            'class' => \yii\grid\ActionColumn::class
        ]
    ]
])?>
