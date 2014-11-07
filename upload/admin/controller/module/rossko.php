<?php

class ControllerModuleRossko extends Controller {

    public function index() {
        $this->load->model('setting/setting');
        $this->load->model('catalog/category');

        $this->data['heading_title'] = 'Rossko';
        $this->data['action'] = $this->url->link('module/rossko', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['conf'] = $this->getConfig(array(
            'key1', 'key2', 'region', 'overprice', 'delivery', 'category'
        ));

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->data['conf'] = $this->request->post;
            $this->setConfig($this->request->post);
            $this->cache->delete('rossko');
        }

        $categories = $this->model_catalog_category->getCategoriesByParentId();
        $this->data['categories'] = array();

        foreach ($categories as $category) {
            $this->data['categories'][] = $category;
        }

        $this->template = 'module/rossko.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->document->setTitle($this->data['heading_title']);
        $this->response->setOutput($this->render());
    }

    public function install() {

    }


    public function uninstall() {

    }

    private function getConfig($keys) {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->config->get('rossko_' . $key);
        }

        return $result;
    }

    private function setConfig($data) {
        $config = array();

        foreach ($data as $key => $value) {
            $config['rossko_' . $key] = $value;
        }

        $this->model_setting_setting->editSetting('rossko', $config);
    }

}
