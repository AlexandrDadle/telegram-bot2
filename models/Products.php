<?php

namespace app\models;


use app\components\CheckIncomeMessage;
use dicr\telegram\entity\Update;
use dicr\telegram\TelegramModule;
use phpDocumentor\Reflection\Types\Static_;
use Symfony\Component\Console\Helper\ProcessHelper;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 *
 * @property int $id [int]
 * @property int $unit_id [int]  ID jedostek opakowania
 * @property string $produkt_type [varchar(255)]  Typ produktu
 * @property string $produkt_name [varchar(255)]  Nazwa produktu
 * @property float $cena_brutto [float]  Cena sprzedaży
 * @property float $cena_netto [float]  Cena zakupu
 * @property float $stawka_vat [float]
 * @property int $in_stock [int]  Ilość dostępnego towaru
 */
class Products extends ActiveRecord
{
    public const TYPE_PRODUCT_ZIOLO = 0;
    public const TYPE_PRODUCT_BIALKO = 1;
    public const TYPE_PRODUCT_KWAS = 2;
    public const TYPE_PRODUCT_GASH = 3;


    public static function tableName()
    {
//        return 'bot_products';
        return '{{%products}}';
    }

    public function rules()
    {
        return [
            [['produkt_type', 'produkt_name', 'cena_brutto', 'cena_netto', 'in_stock'], 'required']
        ];
    }

    /**
     * @param Update $update miejsce skąd się podciągają dane dla tworzenia produktu
     * @return Products
     */
    public static function create(Update $update): Products
    {
        $newProduct = new Products();
        $newProduct->produkt_type = $update->prodType;
        $newProduct->produkt_name = $update->prodName;
        $newProduct->cena_brutto = $update->prodBrutto;
        $newProduct->cena_netto = $update->prodNetto;
        $newProduct->in_stock = $update->prodCount;
        $newProduct->save();

        return $newProduct;
    }

    public function attributeLabels()
    {
        return [
            self::TYPE_PRODUCT_ZIOLO => 'Шишки',
            self::TYPE_PRODUCT_BIALKO => 'Скорость',
            self::TYPE_PRODUCT_KWAS => 'ЛСД',
            self::TYPE_PRODUCT_GASH => 'Гашиш',
            'produkt_type' => 'Тип продукта',
            'produkt_name' => 'Название продукта',
            'cena_brutto' => 'Цена брутто',
            'cena_netto' => 'Цена нетто',
            'in_stock' => 'Количество товара',
        ];
    }

    public static function getProductsByTypeForKeyboard($type)
    {
        $productName = [];
        $keyboard = [];
        $products = Products::find()->where(['produkt_type' => $type]);
        /** @var Products $product */
        foreach ($products->each() as $product) {
            $keyboard[] = array($product->getProduktName());
        }
        $keyboard[] = ['Назад'];
        return $keyboard;
    }

    /**
     * @return mixed
     */
    public function getBrutto()
    {
        return $this->cena_brutto;
    }

    /**
     * @return mixed
     */
    public function getNetto()
    {
        return $this->cena_netto;
    }

    public function getProductDataForEditing(): array
    {
        $data = [];
        $data[''] = $this->getBrutto();
        $data[] = $this->getNetto();
        $data[] = $this->getInStock();

        return $data;
    }

    public static function getProductMenuForKeyboardByName($name): array
    {
        $keyboard = [];
        $product = Products::findOne(['produkt_name' => $name]);
        foreach ($product->getProductDataForEditing() as $value){
            $keyboard[] = [strval($value)];
        }
        $keyboard[] = ['Назад'];

        return $keyboard;
    }


    /**
     * @return string
     */
    public function getProduktName(): string
    {
        return $this->produkt_name;
    }

    /**
     * @return int
     */
    public function getInStock(): int
    {
        return $this->in_stock;
    }

    public static function getAllCountInStockByType($type)
    {
        $inStock = 0;
        $products = Products::find()->where(['produkt_type' => $type])->all();
        foreach ($products as $value) {
            $inStock += $value->getInStock();
        }
        return $inStock;
    }

    public static function getAllTypesOfProduct()
    {
        return [
            self::TYPE_PRODUCT_ZIOLO,
            self::TYPE_PRODUCT_GASH,
            self::TYPE_PRODUCT_KWAS,
            self::TYPE_PRODUCT_BIALKO,
        ];
    }

}