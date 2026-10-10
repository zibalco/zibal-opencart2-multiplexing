<?php 

class ControllerPaymentZibal extends Controller
{
	private $error = array ();

	public function index()
	{
		$this->load->language('payment/zibal');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

			$this->model_setting_setting->editSetting('zibal', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
        $data['text_edit'] = $this->language->get( 'text_edit' );

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$data['help_failed_status'] = $this->language->get('help_failed_status');
		$data['text_none'] = $this->language->get('text_none');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_zibal_direct'] = $this->language->get('entry_zibal_direct');
		$data['placeholder_merchant'] = $this->language->get('placeholder_merchant');

		$data['entry_submerchant1'] = $this->language->get('entry_submerchant1');
		$data['entry_submerchant2'] = $this->language->get('entry_submerchant2');
		$data['entry_submerchant3'] = $this->language->get('entry_submerchant3');
		$data['entry_submerchant4'] = $this->language->get('entry_submerchant4');
		$data['entry_submerchant5'] = $this->language->get('entry_submerchant5');


		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/zibal', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('payment/zibal', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : false;
		$data['error_merchant'] = isset($this->error['merchant']) ? $this->error['merchant'] : false;
		

		if (isset($this->request->post['zibal_merchant'])) {
			$data['zibal_merchant'] = $this->request->post['zibal_merchant'];
		} else {
			$data['zibal_merchant'] = $this->config->get('zibal_merchant');
		}

		if (isset($this->request->post['zibal_direct'])) {
			$data['zibal_direct'] = $this->request->post['zibal_direct'];
		} else {
			$data['zibal_direct'] = $this->config->get('zibal_direct');
		}


		if (isset($this->request->post['zibal_order_status_id'])) {
			$data['zibal_order_status_id'] = $this->request->post['zibal_order_status_id'];
		} else {
			$data['zibal_order_status_id'] = $this->config->get('zibal_order_status_id');
		}

		if (isset($this->request->post['zibal_failed_status_id'])) {
			$data['zibal_failed_status_id'] = $this->request->post['zibal_failed_status_id'];
		} else {
			$data['zibal_failed_status_id'] = $this->config->get('zibal_failed_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['zibal_status'])) {

			$data['zibal_status'] = $this->request->post['zibal_status'];

		} else {

			$data['zibal_status'] = $this->config->get('zibal_status');
		}

		if (isset($this->request->post['zibal_sort_order'])) {

			$data['zibal_sort_order'] = $this->request->post['zibal_sort_order'];

		} else {

			$data['zibal_sort_order'] = $this->config->get('zibal_sort_order');
		}

		// Submerchants
		if (isset($this->request->post['zibal_submerchant1'])) {
			$data['zibal_submerchant1'] = $this->request->post['zibal_submerchant1'];
		} else {
			$data['zibal_submerchant1'] = $this->config->get('zibal_submerchant1');
		}

		if (isset($this->request->post['zibal_submerchant2'])) {
			$data['zibal_submerchant2'] = $this->request->post['zibal_submerchant2'];
		} else {
			$data['zibal_submerchant2'] = $this->config->get('zibal_submerchant2');
		}

		if (isset($this->request->post['zibal_submerchant3'])) {
			$data['zibal_submerchant3'] = $this->request->post['zibal_submerchant3'];
		} else {
			$data['zibal_submerchant3'] = $this->config->get('zibal_submerchant3');
		}

		if (isset($this->request->post['zibal_submerchant4'])) {
			$data['zibal_submerchant4'] = $this->request->post['zibal_submerchant4'];
		} else {
			$data['zibal_submerchant4'] = $this->config->get('zibal_submerchant4');
		}

		if (isset($this->request->post['zibal_submerchant5'])) {
			$data['zibal_submerchant5'] = $this->request->post['zibal_submerchant5'];
		} else {
			$data['zibal_submerchant5'] = $this->config->get('zibal_submerchant5');
		}

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/zibal.tpl', $data));
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/zibal')) {

			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['zibal_merchant']) {
			
			$this->error['warning'] = $this->language->get('error_validate');
			$this->error['merchant'] = $this->language->get('error_merchant');
		}


		if (!$this->error) {

			return true;

		} else {

			return false;
		}
	}
}
