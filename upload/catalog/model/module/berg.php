<?php

class ModelModuleBerg extends Model {

    public function searchProducts($query) {
        $query_string = array(
            'key'     => $this->config->get('berg_key'),
            'analogs' => $this->config->get('berg_analogs') ? 1 : 0
        );

        $query_string['items'][0] = array(
            'resource_article' => $query
        );

        $products = array();
        $answer = $this->sendRequest($query_string);

        foreach ($answer->resources as $resource) {
            $product = $this->prepareProduct($resource);

            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }

    public function saveProduct($product_data) {
        $admin = $this->injectAdminModel('catalog/product');
        $category = array($this->config->get('berg_category'));

        $product = array(
            'quantity' => $product_data['quantity'],
            'sku'      => $product_data['article'],
            'price'    => $product_data['price'],
            'model'    => $product_data['uid'],
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

    public function getProduct($uid) {
        $query_string = array(
            'key' => $this->config->get('berg_key'),
        );

        $query_string['items'][0] = array(
            'resource_id' => $uid
        );

        $answer = $this->sendRequest($query_string);
        return $this->prepareProduct($answer->resources[0]);
    }

    private function prepareProduct($resource) {
        $conf_overprice = $this->config->get('berg_overprice');
        $conf_delivery = $this->config->get('berg_delivery');

        $offer = $this->getBestOffer($resource->offers);
        $product = null;

        if ($offer) {
            $product = array(
                'uid'      => $resource->id,
                'name'     => $resource->name,
                'article'  => $resource->article,
                'brand'    => $resource->brand->name,
                'quantity' => $offer->quantity,
            );

            if ($conf_overprice) {
                if (strpos($conf_overprice, '%')) {
                    $product['price'] = $offer->price + ($offer->price / 100 * intval($conf_overprice));
                } else {
                    $product['price'] += $offer->price + $conf_overprice;
                }
            } else {
                $product['price'] = $offer->price;
            }

            if ($conf_delivery) {
                $product['delivery'] = $offer->assured_period + $conf_delivery;
            } else {
                $product['delivery'] = $offer->assured_period;
            }
        }

        return $product;
    }

    private function sendRequest($query_string) {
        $answer_json = file_get_contents('http://api.berg.ru/ordering/get_stock.json?' . http_build_query($query_string, '', '&', PHP_QUERY_RFC1738));
        return json_decode($answer_json);
    }

    private function getBestOffer($offers) {
        $cleaned_offers = array();
        foreach ($offers as $offer) {
            if ($offer->price > 0 && $offer->quantity > 0) {
                $cleaned_offers[] = $offer;
            }
        }

        usort($cleaned_offers, function($a, $b) {
            return $a->price - $b->price;
        });

        return count($cleaned_offers) > 0 ? $cleaned_offers[0] : null;
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
