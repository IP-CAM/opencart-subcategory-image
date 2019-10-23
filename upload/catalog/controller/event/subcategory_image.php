<?php

/* 
 * 
 */
class ControllerEventSubcategoryImage extends Controller {
    public function after_index(&$route, &$data = array(), &$output = '') {
        $this->load->model('catalog/category');
        $this->load->model('setting/setting');
        // check if this module is enabled
        if(!$this->config->get('module_subcategory_image_status')) {
            $data['column_count'] = 4;
            return;
        }
        // category image flag
        $data['category_image_flag'] = true;
        // column count
        $data['column_count'] = $this->config->get('module_subcategory_image_column');
        
        if (isset($this->request->get['path'])) {
            $url = '';

            if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
            }
            $parts = explode('_', (string)$this->request->get['path']);
            $category_id = (int)array_pop($parts);
        } else {
            $category_id = 0;
        }
        $results = $this->model_catalog_category->getCategories($category_id);
        
        $data['categories'] = array();
        $width = $this->config->get('module_subcategory_image_width');
        $height = $this->config->get('module_subcategory_image_height');
        foreach ($results as $result) {
                if ($result['image'] && is_file(DIR_IMAGE . $result['image'])) {
                        $thumb = $this->model_tool_image->resize($result['image'], $width, $height);
                } else {
                        $thumb = $this->model_tool_image->resize($this->model_setting_setting->getSettingValue('module_subcategory_image_no_image'), $width, $height);
                }
                $data['categories'][] = array(
						'category_id' => $result['category_id'],
                        'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
                        'thumb' => $thumb
                );
        }
    }
}
