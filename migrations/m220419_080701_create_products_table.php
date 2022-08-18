<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m220419_080701_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'unit_id' => $this->integer(10),
            'produkt_type' => $this->string(),
            'produkt_name' => $this->string(),
            'cena_brutto' => $this->float(),
            'cena_netto' => $this->float(),
            'stawka_vat' => $this->float(),
            'in_stock' => $this->integer(),
        ]);
        $this->addCommentOnColumn('{{%products}}', 'unit_id', 'ID jedostek opakowania');
        $this->addCommentOnColumn('{{%products}}', 'produkt_type', 'Typ produktu');
        $this->addCommentOnColumn('{{%products}}', 'produkt_name', 'Nazwa produktu');
        $this->addCommentOnColumn('{{%products}}', 'cena_brutto', 'Cena sprzedaży');
        $this->addCommentOnColumn('{{%products}}', 'cena_netto', 'Cena zakupu');
        $this->addCommentOnColumn('{{%products}}', 'in_stock', 'Ilość dostępnego towaru');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%products}}');
    }
}
