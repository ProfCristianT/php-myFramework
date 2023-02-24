<?php
namespace app\products;

use vendor\abstracts\Repository;


class ProductRepository extends Repository{
    public function __construct(){
        // $this->entity = "products";
        // $this->modelClassName = "app\products\ProductModel";
        parent::__construct("products", "app\products\ProductModel");
    }

}




//$product = (new ProductRepository)->findOne(["brand"=>"Motorola"]);


// $product = (new ProductRepository)->create([
//     "brand"=> "Samsung",
//     "model"=> "S32a",
//     "explanation"=> "lorem ..asda.sd.as.d.asd.a.sd.as.d .as d.as. d.a s",
//     "price"=> 125000,
// ]);

//(new ProductRepository)->update([],[]);
// $product = (new ProductRepository)->update( ["id"=> 28] , [
//     "price"=> 190000
// ]);
// (new ProductRepository)->delete([]);
// $product = (new ProductRepository)->delete( ["id"=> 1] );
