<?php 
namespace app\products;

//use app\products\ProductRepository as Repository;

use vendor\middlewares\DefaultRouter;

class ProductsRoutes{
    private DefaultRouter $router;

    public function getRouter(){
        return $this->router;
    }

    public function __construct(){
        $this->router = new DefaultRouter;

        $this->router->get("/products", $this->getProducts() );
        $this->router->get("/products/:productId", $this->getProductById() );
        $this->router->post("/products", $this->createProduct() );
        $this->router->put("/products/:productId", $this->putProduct() );
        $this->router->patch("/products/:productId", $this->patchProduct() );
        $this->router->delete("/products/:productId", $this->deleteProduct() );

    }

    private function getProducts(){
        return function(){
            $products = (new ProductRepository)->find();
            // echo "<pre>";
            // var_dump($products);
            // echo "</pre>";
            header("Content-Type: application/json");
            echo json_encode($products);
        };
    }

    private function getProductById(){
        return function( $request ){
            //echo "Ver producto: ".$request->params["productId"];
            $id = $request->params["productId"];
            $product = (new ProductRepository)->findOne(["id" => $id]);
            header("Content-Type: application/json");
            echo $product? json_encode($product) : "{}" ;
        };
    }

    private function createProduct(){
        return function(){
            header("Content-Type: application/json");
            if($_POST){
                $product= new ProductModel(
                            $_POST["brand"], 
                            $_POST["model"], 
                            $_POST["price"], 
                            $_POST["category"], 
                            $_POST["explanation"], 
                            $_POST["img"]
                        );

                $product->save();
                
                echo json_encode($product->convertToArray());
            }
        };
    }

    private function putProduct(){
        return function($request){
            $_PUT = getData();
           var_dump($_PUT);

            header("Content-Type: application/json");
            if($_PUT){
                $product= new ProductModel(
                            $_PUT["brand"], 
                            $_PUT["model"], 
                            $_PUT["price"], 
                            $_PUT["category"], 
                            $_PUT["explanation"], 
                            $_PUT["img"],
                            $request->params["productId"]
                        );

                $product->save();
                
                echo json_encode($product->convertToArray());
            }
        };
    }

    private function patchProduct(){
        return function($request){
            header("Content-Type: application/json");
            if($_POST){
                $product= new ProductModel(
                            $_POST["brand"], 
                            $_POST["model"], 
                            $_POST["price"], 
                            $_POST["category"], 
                            $_POST["explanation"], 
                            $_POST["img"],
                            $request->params["productId"]
                        );

                $product->save();
                
                echo json_encode($product->convertToArray());
            }
        };
    }

    private function deleteProduct(){
        return function($request){
            echo "Borrar producto: ".$request->params["productId"];
        };
    }
}



// $router = new DefaultRouter;

// $router->get("/home", function( $request ){
//     echo "Hola, estas en HOME";
// });

// $router->get("/products", function( $request ){
//     echo "Ver todos los productos";
// });

// $router->get("/products/:productId", function( $request ){
//     echo "Ver producto: ".$request->params["productId"];
//     //var_dump($request->params["productId"]);
// });

// $router->post("/products", function( $request ){
//     echo "Crear un producto";

// });

// $router->patch("/products/:productId", function( $request ){
//     echo "Editar producto: ".$request->params["productId"];

// });

// $router->delete("/products/:productId", function( $request ){
//     echo "Borrar producto: ".$request->params["productId"];

// });