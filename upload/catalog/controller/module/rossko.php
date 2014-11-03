<?php

class ControllerModuleRossko extends Controller {

    public function search() {
        $this->load->model('module/rossko');
        $this->data['products'] = array();

        if (isset($this->request->get['query'])) {
            $this->data['query'] = $query = $this->request->get['query'];
            $this->data['products'] = $this->model_module_rossko->searchProduct($query);
        }

        if (file_exists(DIR_TEMPLATE . 'catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/rossko.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/rossko.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/rossko.css');
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/rossko_results.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/rossko_results.tpl';
        } else {
            $this->template = 'default/template/module/rossko_results.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function addToCart() {
        $this->load->model('module/rossko');

        $product_uid = $this->request->post['product_uid'];
        $quantity = $this->request->post['quantity'];

        $product_data = $this->model_module_rossko->getProduct($product_id);

        if ($product_data) {
            $product_id = $product_admin->addProduct();
            $this->cart->add($product_id, $quantity);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($product_data));
    }

}
