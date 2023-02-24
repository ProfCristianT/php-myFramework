<?php
namespace app\products;
use vendor\abstracts\Model;
use Exception;


 
class ProductModel extends Model{
    public function __construct(
        protected string|null $brand = "",
        protected string|null $model = "",
        protected float|null $price = 0,
        protected string|null $category = "",
        protected string|null $explanation = "",
        protected string|null $img = "",
        protected string|int|null $id = null,
        //protected bool $isNew = true 
        bool $isNew = true 
    ){
        $identifier = "id";
        //$repository = "app\\products\\ProductRepository";
        $repository = new ProductRepository;
        parent::__construct($isNew, $identifier, $repository);
        $this->validateBrand($brand);

    }

    //Brand-----------------------------------------------------------
    public function getBrand(){
        return $this->brand;
    }

    public function setBrand($brand){
        $this->validateBrand($brand);
    }

    //Validate -------------------------------------------------------
    private function validateBrand($brand){
        $brand = trim($brand);
        $this->brand = $brand;
        if( preg_match("/^[\w\s]{2,}$/", $brand) ){
        }
        else{
            //throw new Exception("No coincide la cadena con brand");
        }
    }

}