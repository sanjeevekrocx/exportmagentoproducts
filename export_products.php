<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;

try{

    require __DIR__ . '/app/bootstrap.php';

    $params = $_SERVER;
    $bootstrap = Bootstrap::create(BP, $params);
    $obj = $bootstrap->getObjectManager();
    $obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
    $appState = $obj->get('\Magento\Framework\App\State');
    $appState->setAreaCode('frontend');

    $products = $obj->create('\Magento\Catalog\Model\Product')->getCollection();
    $products->addAttributeToSelect('*')
        ->addFieldTofilter('type_id','simple')
        ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->load();

 
    $all_data = array();
    $all_data[] = array('Stock Code','Sales Price','Description','Unit of Sale','Location','Nominal A/C Ref','Supplier A/C Ref');
    $i=1;
    foreach ($products as $product) {
        $removeslash=str_replace(' / ','',$product->getsku());
        $removeslashw=str_replace(' /','',$removeslash);
        $removehyphen=str_replace('-','',$removeslashw);
        $removespace=str_replace(' ','',$removehyphen);
        $removesymbol=str_replace('x','',$removespace);
        $sku="IR".$removesymbol;
        $price=$product->getPrice();
        //$description=$product->getDescription();
        $name=$product->getName();
        $unitofsale='Each';
        $location= 'Warehouse A1';
        $Nominal='4000';
        $Supplier='Mike';
        $all_data[] = array($sku,$price,$name,$unitofsale,$location,$Nominal,$Supplier);
    }

    $file = fopen("rm365sagaproductimport.csv","w");
    $url = "rm365sagaproductimport.csv"; 

    $file_name = basename($url);  
    $info = pathinfo($file_name); 
    if ($info["extension"] == "csv") { 
      header("Content-Description: File Transfer"); 
      header("Content-Type: application/octet-stream"); 
      header("Content-Disposition: attachment; filename=\"". $file_name . "\""); 
      if ($products->getSize() > 0) {
             foreach ($all_data as $line) {
                fputcsv($file, $line);
             }
             fclose($file);
             echo "File downloaded successfully";
         }
      readfile ($url); 
    } 

        } catch (Exception $e) {
            echo $e->getMessage();
        }
?>
