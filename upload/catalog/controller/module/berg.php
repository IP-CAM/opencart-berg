<?php

class ControllerModuleBerg extends Controller {

    public function search() {
        $this->load->model('module/berg');

        $this->data['query'] = '';
        $this->data['products'] = array();
        $document_title = 'Поиск автозапчастей';

        if (isset($this->request->get['query'])) {
            $this->data['query'] = $query = $this->request->get['query'];
            $this->data['products'] = $this->model_module_berg->searchProducts($query);
            $document_title = "{$query} — {$document_title}";
        }

        if (file_exists(DIR_TEMPLATE . 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/berg.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/berg.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/berg.css');
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/berg.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/berg.tpl';
        } else {
            $this->template = 'default/template/module/berg.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->document->setTitle($document_title);
        $this->response->setOutput($this->render());
    }

    public function addToCart() {
        $this->load->model('module/berg');
        $this->language->load('checkout/cart');
        $this->load->model('setting/extension');

        $uid = $this->request->post['product_uid'];
        $quantity = $this->request->post['quantity'];

        $product_data = $this->model_module_berg->getProduct($uid);

        if ($product_data) {
            $product_id = $this->model_module_berg->saveProduct($product_data);
            $this->cart->add($product_id, $quantity);
        }

        $total = 0;
        $total_data = array();
        $taxes = $this->cart->getTaxes();

        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }
        }

        $json = array(
            'product' => $product_data,
            'total'   => sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total))
        );

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
