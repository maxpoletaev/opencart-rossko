<?php

class ModelModuleRossko extends Model {

    public function searchProduct($query_text) {
        $cache_key = 'rossko.search.' . md5($query_text);
        $region = $this->config->get('rossko_region');
        $products = $this->cache->get($cache_key);

        if (! $products) {
            $client = new SoapClient("http://{$region}.rossko.ru/service/v1/GetSearch?wsdl");
            $products = array();

            $query = $client->GetSearch(array(
                'KEY1' => $this->config->get('rossko_key1'),
                'KEY2' => $this->config->get('rossko_key2'),
                'TEXT' => $query_text
            ));

            $searchResult = $query->SearchResults->SearchResult;

            if ($searchResult->Success && isset($searchResult->PartsList)) {
                $conf_overprice = $this->config->get('rossko_overprice');
                $conf_delivery = $this->config->get('rossko_delivery');

                $parts = $searchResult->PartsList->Part;

                if (!is_array($parts)) {
                    $parts = array($parts);
                }

                foreach ($parts as $part) {
                    $product = array(
                        'uid'      => $part->GUID,
                        'name'     => $part->Name,
                        'brand'    => $part->Brand,
                        'code'     => $part->PartNumber,
                        'quantity' => null,
                        'price'    => null,
                        'delivery' => null,
                        'stocks'   => array(),
                    );

                    if (isset($part->StocksList->Stock)) {
                        $prices = array();
                        $deliveries = array();
                        $quantities = array();

                        $stocks = $part->StocksList->Stock;

                        if (!is_array($stocks)) {
                            $stocks = array($stocks);
                        }

                        array_map(function($stock) use (&$prices, &$deliveries, &$quantities, $conf_overprice, $conf_delivery) {
                            if ($stock->Price > 0) {
                                if (strpos($conf_overprice, '%')) {
                                    $price = $stock->Price + ($stock->Price / 100 * intval($conf_overprice));
                                } else {
                                    $price = $stock->Price + $conf_overprice;
                                }

                                $prices[] = $price;
                                $quantities[] = $stock->Count;
                                $deliveries[] = $stock->DeliveryTime + $conf_delivery;
                            }
                        }, $stocks);

                        $product['price'] = (count($prices) > 1) ? min($prices) : $prices[0];
                        $product['quantity'] = (count($quantities) > 1) ? max($quantities) : $quantities[0];
                        $product['delivery'] = (count($deliveries) > 1) ? min($deliveries) : $deliveries[0];
                    }

                    $products[] = $product;
                }
            }

            $this->pushToIndex($products);
            $this->cache->set($cache_key, $products);
        }

        return $products;
    }

    public function getProduct($uid) {
        $index = $this->cache->get('rossko.index');

        if (!$index || !isset($index[$uid])) {
            return false;
        }

        return $index[$uid];
    }

    public function saveProduct($product_data) {
        $admin = $this->injectAdminModel('catalog/product');
        $category = array($this->config->get('rossko_category'));

        $product = array(
            'quantity' => $product_data['quantity'],
            'price'    => $product_data['price'],
            'model'    => $product_data['code'],
            'sku'      => $product_data['uid'],
            'ean'      => '',
            'jan'      => '',
            'isbn'     => '',
            'mpn'      => '',
            'upc'      => '',
            'location' => '',
            'minimum'  => '',
            'subtract' => '',
            'shipping' => '',
            'points'   => '',
            'weight'   => '',
            'width'    => '',
            'height'   => '',
            'keyword'  => '',
            'length'   => '',
            'status'   => 1,

            'sort_order'       => 100,
            'date_available'   => date('Y-m-d'),
            'product_category' => $category,
            'product_store'    => array(0),
            'manufacturer_id'  => 0,
            'weight_class_id'  => 0,
            'stock_status_id'  => 7,
            'length_class_id'  => 0,
            'tax_class_id'     => 0,
        );

        $product['product_description'][1] = array(
            'tag'              => '',
            'name'             => $product_data['name'],
            'seo_h1'           => '',
            'seo_title'        => '',
            'description'      => '',
            'meta_keyword'     => '',
            'meta_description' => '',
        );

        return $admin->addProduct($product);
    }

    private function pushToIndex($products) {
        $cache_key = 'rossko.index';
        $index = $this->cache->get($cache_key);

        if (!$index) {
            $index = array();
        }

        foreach ($products as $product) {
            $index[$product['uid']] = $product;
        }

        $this->cache->set($cache_key, $index);
    }

    private function injectAdminModel($model) {
        $file = DIR_APPLICATION . '../admin/model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);
            return new $class($this->registry);
        }
    }

}
