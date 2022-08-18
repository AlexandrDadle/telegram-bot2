<?php

namespace app\controllers;

use app\models\Products;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProductController extends Controller
{
    public function actionCreate()
    {
        $model = new Products();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                return $this->redirect(['product/product-list']);
            }
        }
        return $this->render('product-list', ['products' => $model]);
    }
    public function actionUpdate($id)
    {
        $model = Products::findOne($id);
        if ($model){
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save(false)) {
                    return $this->redirect(['product/product-list']);
                }
            }
            return $this->render('product-list', ['products' => $model]);
        }
        throw new NotFoundHttpException('Pusto');
    }

    public function actionProductList()
    {
        $query = Products::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $this->render('show-all-products', ['dataprovider' => $dataProvider]);
    }

    public function actionDelete($id)
    {
        $product = Products::find()->where(['id' => $id])->one();
        if ($product){
            $product->delete();
        }
        return $this->redirect(['product/product-list']);
    }
}