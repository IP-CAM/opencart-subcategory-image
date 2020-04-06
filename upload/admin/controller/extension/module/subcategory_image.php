<?php
class ControllerExtensionModuleSubcategoryImage extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/subcategory_image');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_subcategory_image', $this->request->post);
                        

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
                // Error codes
		if (isset($this->error['no_image'])) {
			$data['error_no_image'] = $this->error['no_image'];
		} else {
			$data['error_no_image'] = '';
		}
		if (isset($this->error['column'])) {
			$data['error_column'] = $this->error['column'];
		} else {
			$data['error_column'] = '';
		}
		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}
		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/subcategory_image', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/subcategory_image', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_subcategory_image_no_image'])) {
			$data['module_subcategory_image_no_image'] = $this->request->post['module_subcategory_image_no_image'];
		} else {
			$data['module_subcategory_image_no_image'] = $this->config->get('module_subcategory_image_no_image');
		}

		if (isset($this->request->post['module_subcategory_image_status'])) {
			$data['module_subcategory_image_status'] = $this->request->post['module_subcategory_image_status'];
		} else {
			$data['module_subcategory_image_status'] = $this->config->get('module_subcategory_image_status');
		}

		if (isset($this->request->post['module_subcategory_image_width'])) {
			$data['module_subcategory_image_width'] = $this->request->post['module_subcategory_image_width'];
		} else {
			$data['module_subcategory_image_width'] = $this->config->get('module_subcategory_image_width');
		}

		if (isset($this->request->post['module_subcategory_image_height'])) {
			$data['module_subcategory_image_height'] = $this->request->post['module_subcategory_image_height'];
		} else {
			$data['module_subcategory_image_height'] = $this->config->get('module_subcategory_image_height');
		}

		if (isset($this->request->post['module_subcategory_image_column'])) {
			$data['module_subcategory_image_column'] = $this->request->post['module_subcategory_image_column'];
		} else {
			$data['module_subcategory_image_column'] = $this->config->get('module_subcategory_image_column');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
                // get thumbnail
                $this->load->model('tool/image');

		if (is_file(DIR_IMAGE . $this->model_setting_setting->getSettingValue('module_subcategory_image_no_image'))) {
			$data['thumb'] = $this->model_tool_image->resize($this->model_setting_setting->getSettingValue('module_subcategory_image_no_image'), 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                        $data['module_subcategory_image_no_image'] = 'no_image.png';
		}
                
		$this->response->setOutput($this->load->view('extension/module/subcategory_image', $data));
	}
        
        public function install() {
            $this->load->model('setting/event');
            $this->model_setting_event->addEvent('subcategory_image','catalog/view/product/category/before','extension/event/subcategory_image/after_index');
            
        }
        
        public function uninstall() {
            $this->load->model('setting/event');
            $this->model_setting_event->deleteEventByCode('subcategory_image');
        }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/subcategory_image')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['module_subcategory_image_no_image']) {
			$this->error['no_image'] = $this->language->get('error_no_image');
		}

		if (!preg_match('/^\d+$/', $this->request->post['module_subcategory_image_column']) || 
                        (int)$this->request->post['module_subcategory_image_column'] < 1 ||
                        (int)$this->request->post['module_subcategory_image_column'] > 6) {
			$this->error['column'] = $this->language->get('error_column');
		}

		if (!preg_match('/^\d+$/', $this->request->post['module_subcategory_image_width'])) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!preg_match('/^\d+$/', $this->request->post['module_subcategory_image_height'])) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}