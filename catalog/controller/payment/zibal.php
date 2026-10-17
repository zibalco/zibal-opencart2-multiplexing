<?php

	class ControllerPaymentZibal extends Controller {
		// Sent in the User-Agent so Zibal can tell which plugin and version made the request.
		const PLUGIN_NAME = 'Zibal-OpenCart2-Multiplexing';
		const PLUGIN_VERSION = '1.0.0';

		// Raw result of the last call to Zibal, kept for the order history notes.
		private $last_response = array('http_code' => 0, 'error' => '', 'body' => '');

		public function index() {
			$this->load->language('payment/zibal');
			$this->load->model('checkout/order');
			$this->load->model('payment/zibal');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			$data['button_confirm'] = $this->language->get('button_confirm');

			$data['error_warning'] = false;

			if (extension_loaded('curl')) {

				$amount = $this->get_price_in_rail($order_info);
				$redirect = str_replace('&amp;', '&', $this->url->link('payment/zibal/callback', 'order_id=' . $order_info['order_id'] . '&token=' . $this->get_token($order_info['order_id']), 'SSL'));

				$merchant = $this->config->get('zibal_merchant');
				$zibal_direct = $this->config->get('zibal_direct');
				
				$sub_list = array();
				$sub_count = 0;
				if ($this->config->get('zibal_submerchant1') != "") {
					$sub1 = $this->config->get('zibal_submerchant1');
					$sub_count++;
					array_push($sub_list, $sub1);
				}
				if ($this->config->get('zibal_submerchant2') != "") {
					$sub2 = $this->config->get('zibal_submerchant2');
					$sub_count++;
					array_push($sub_list, $sub2);
				}
				if ($this->config->get('zibal_submerchant3') != "") {
					$sub3 = $this->config->get('zibal_submerchant3');
					$sub_count++;
					array_push($sub_list, $sub3);
				}
				if ($this->config->get('zibal_submerchant4') != "") {
					$sub4 = $this->config->get('zibal_submerchant4');
					$sub_count++;
					array_push($sub_list, $sub4);
				}
				if ($this->config->get('zibal_submerchant5') != "") {
					$sub5 = $this->config->get('zibal_submerchant5');
					$sub_count++;
					array_push($sub_list, $sub5);
				}

				if(count($sub_list) == 0) {
					$parameters = array(
						'merchant' => $merchant,
						'amount' => $amount,
						'orderId' => $order_info['order_id'],
						'mobile' => $order_info['telephone'],
						'description' => $order_info['email'],
						'callbackUrl' => $redirect,
					);
				} else {
					$sub_count = count($sub_list);
					$multiplexingInfos = array();
					if($sub_count == 3) {
						$multiplexingInfos = array(
							array(
								"amount" => 33, 
								"bankAccount" => $sub_list[0]
							),
							array(
								"amount" => 33, 
								"bankAccount" => $sub_list[1]
							),
							array(
								"amount" => 34, 
								"bankAccount" => $sub_list[2]
							),
						);
					} else {
						$sub_percent = 100/$sub_count;
						foreach ($sub_list as $submerchant) {
							array_push($multiplexingInfos, array(
								"amount" => $sub_percent, 
								"bankAccount" => $submerchant
							));
						}
					}
					$parameters = array(
						'merchant' => $merchant,
						'amount' => $amount,
						'orderId' => $order_info['order_id'],
						'mobile' => $order_info['telephone'],
						'description' => $order_info['email'],
						'callbackUrl' => $redirect,
						'percentMode' => 1,
						'multiplexingInfos' => $multiplexingInfos
					);

				}

				$result = $this->post_to_zibal('request', $parameters);
				if ($result != false && isset($result->result) && $result->result == 100 && isset($result->trackId) && ctype_digit((string)$result->trackId)) {
					// Remember which trackId belongs to this order and amount, so the callback can check it.
					$this->model_payment_zibal->addTransaction($order_info['order_id'], (string)$result->trackId, $amount);

					$this->add_note($order_info, $this->language->get('text_note_request') . $result->trackId, false, $this->config->get('config_order_status_id'));

					$data['action'] = 'https://gateway.zibal.ir/start/' . $result->trackId;
					if($zibal_direct=="yes")
						$data['action'].="/direct";
				} else {
					$this->add_note($order_info, $this->language->get('text_note_request_failed'), true);

					$data['error_warning'] = $this->language->get('error_generic');
				}


			} else {

				$this->add_note($order_info, $this->language->get('text_note_curl'));

				$data['error_warning'] = $this->language->get('error_generic');
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/zibal.tpl')) {

				return $this->load->view($this->config->get('config_template') . '/template/payment/zibal.tpl', $data);

			} else {

				return $this->load->view('default/template/payment/zibal.tpl', $data);
			}
		}

		public function callback() {
			$this->load->language('payment/zibal');
			$this->load->model('checkout/order');
			$this->load->model('payment/zibal');

			$this->document->setTitle($this->language->get('heading_title'));

			// Prefer the signed order id from the callback URL; fall back to the session.
			$order_id = false;

			if (isset($this->request->get['order_id'], $this->request->get['token']) && $this->check_token($this->request->get['order_id'], $this->request->get['token'])) {
				$order_id = (int)$this->request->get['order_id'];
			} elseif (isset($this->session->data['order_id'])) {
				$order_id = $this->session->data['order_id'];
			}

			$order_info = $order_id ? $this->model_checkout_order->getOrder($order_id) : false;

			$data['heading_title'] = $this->language->get('heading_title');

			$data['button_continue'] = $this->language->get('button_continue');
			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			$data['error_warning'] = false;

			$data['continue'] = $this->url->link('checkout/cart', '', 'SSL');
			$merchant = $this->config->get('zibal_merchant');

			// Zibal returns the customer to the callback URL with GET parameters.
			$success = isset($this->request->get['success']) ? $this->request->get['success'] : (isset($this->request->post['success']) ? $this->request->post['success'] : '');
			$trackId = isset($this->request->get['trackId']) ? $this->request->get['trackId'] : (isset($this->request->post['trackId']) ? $this->request->post['trackId'] : '');
			$orderId = isset($this->request->get['orderId']) ? $this->request->get['orderId'] : (isset($this->request->post['orderId']) ? $this->request->post['orderId'] : '');

			// A paid order stays paid: reopening the callback link, with any parameters, never calls verify
			// again, never writes to the order history and always shows the success page.
			$paid_transaction = $order_info ? $this->model_payment_zibal->getPaidTransaction($order_info['order_id']) : false;

			if ($paid_transaction) {
				if (is_string($trackId) && ctype_digit($trackId) && $trackId !== $paid_transaction['track_id']) {
					// A second payment attempt for an order that is already paid is not verified; leave it for the admin.
					$this->log->write('Zibal: order_id ' . $order_info['order_id'] . ' is already paid with trackId ' . $paid_transaction['track_id'] . '; callback for trackId ' . $trackId . ' was ignored and not verified.');
				}

				$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));

				return;
			}

			if ($success !== '' && $trackId !== '' && $orderId !== '') {
				if ($success == 1) {
					// The trackId must be one we stored for this exact order when the payment was requested.
					$transaction = false;

					if (is_string($trackId) && ctype_digit($trackId)) {
						$transaction = $this->model_payment_zibal->getTransaction($trackId);
					}

					if ($order_info && $transaction && is_string($orderId) && (string)$transaction['order_id'] === (string)$order_info['order_id'] && (string)$order_id === $orderId && $orderId === (string)$order_info['order_id']) {
						$amount = $this->get_price_in_rail($order_info);

						if ($transaction['status'] == 'paid') {
							// Page reloaded after a successful payment: nothing more to do.
						} elseif ($transaction['status'] != 'pending') {
							$this->add_note($order_info, $this->language->get('text_note_status') . $transaction['status'] . "\n" . $this->format_params());

							$data['error_warning'] = $this->language->get('error_generic');
						} elseif (!$amount || $amount != $transaction['amount']) {
							// The order total changed after the payment request was made.
							$this->add_note($order_info, $this->language->get('text_note_amount_changed') . "\n" . $this->format_params());

							$data['error_warning'] = $this->language->get('error_generic');
						} else {
							//verify payment
							$parameters = array(
									'trackId' => $trackId,
									'merchant' => $merchant,
							);

							$result = $this->post_to_zibal('verify', $parameters);

							if (!$result || !isset($result->result)) {
								// Connection failure is not a failed payment; the customer can reload to retry verify.
								$this->add_note($order_info, $this->language->get('text_note_connection'), true);

								$data['error_warning'] = $this->language->get('error_generic');
							} elseif ($result->result == 100) {
								if (!isset($result->amount, $result->orderId) || (int)$result->amount != $transaction['amount'] || (string)$result->orderId !== (string)$order_info['order_id']) {
									// Zibal has settled the money, so keep a record for a manual refund or review.
									$this->model_payment_zibal->updateTransactionStatus($trackId, 'pending', 'review');

									$this->add_note($order_info, $this->language->get('text_note_mismatch'), true);

									$data['error_warning'] = $this->language->get('error_generic');
								} elseif ($this->model_payment_zibal->updateTransactionStatus($trackId, 'pending', 'paid')) {
									// Only the request that flips pending -> paid records the payment.
									$comment  = $this->language->get('text_paid') . "\n";
									$comment .= $this->language->get('text_track_id') . $trackId . "\n";
									$comment .= $this->language->get('text_ref_number') . (isset($result->refNumber) ? $result->refNumber : '-') . "\n";
									$comment .= $this->language->get('text_card_number') . (isset($result->cardNumber) ? $result->cardNumber : '-') . "\n";
									$comment .= $this->language->get('text_paid_at') . (isset($result->paidAt) ? $result->paidAt : '-');

									$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('zibal_order_status_id'), htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'));
								}
							} elseif ($result->result == 201) {
								// Verified earlier but never recorded here (e.g. the first response was lost): needs manual review.
								$this->model_payment_zibal->updateTransactionStatus($trackId, 'pending', 'review');

								$this->add_note($order_info, $this->language->get('text_note_201'), true);

								$data['error_warning'] = $this->language->get('error_generic');
							} else {
								// 202 means Zibal says it was not paid; other codes (e.g. merchant errors) keep it retryable.
								if ($result->result == 202) {
									$this->model_payment_zibal->updateTransactionStatus($trackId, 'pending', 'failed');
								}

								$this->add_note($order_info, $this->language->get('text_note_verify_failed'), true);

								$data['error_warning'] = $this->language->get('error_generic');
							}
						}
					} else {
						$this->add_note($order_info, $this->language->get('text_note_not_found') . "\n" . $this->format_params());

						$data['error_warning'] = $this->language->get('error_generic');
					}
				} else {
					$this->add_note($order_info, $this->language->get('text_note_cancel') . "\n" . $this->format_params());

					$data['error_warning'] = $this->language->get('error_generic');
				}
			} else {
				$this->add_note($order_info, $this->language->get('text_note_data') . "\n" . $this->format_params());

				$data['error_warning'] = $this->language->get('error_generic');
			}

			if ($data['error_warning']) {

				$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
						'text' => $this->language->get('text_home'),
						'href' => $this->url->link('common/home', '', 'SSL'),
						'separator' => false
				);

				$data['breadcrumbs'][] = array(
						'text' => $this->language->get('text_basket'),
						'href' => $this->url->link('checkout/cart', '', 'SSL'),
						'separator' => ' » '
				);

				$data['breadcrumbs'][] = array(
						'text' => $this->language->get('text_checkout'),
						'href' => $this->url->link('checkout/checkout', '', 'SSL'),
						'separator' => ' » '
				);

				$data['header'] = $this->load->controller('common/header');
				$data['footer'] = $this->load->controller('common/footer');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/zibal_callback.tpl')) {

					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/zibal_callback.tpl', $data));

				} else {

					$this->response->setOutput($this->load->view('default/template/payment/zibal_callback.tpl', $data));
				}

			} else {

				$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
			}
		}

		private function post_to_zibal($url, $data = false) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://gateway.zibal.ir/v1/".$url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8'));
			curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent());
			curl_setopt($ch, CURLOPT_POST, 1);
			if ($data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			$error = curl_errno($ch);

			$this->last_response = array(
				'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
				'error' => $error ? $error . ' ' . curl_error($ch) : '',
				'body' => $result === false ? '' : (string)$result
			);

			curl_close($ch);
			$result = !empty($result) ? json_decode($result) : false;
			return is_object($result) ? $result : false;
		}

		private function user_agent() {
			return self::PLUGIN_NAME . '/' . self::PLUGIN_VERSION
				. ' (OpenCart/' . (defined('VERSION') ? VERSION : 'unknown') . '; PHP/' . PHP_VERSION . ')';
		}

		// Adds a note to the order history without changing the order status or emailing the customer.
		private function add_note($order_info, $note, $with_response = false, $order_status_id = false) {
			$comment = $note;

			if ($with_response) {
				$comment .= "\n" . $this->format_response();
			}

			if (!$order_info) {
				$this->log->write('Zibal: ' . str_replace(array("\r", "\n"), ' | ', $comment));

				return;
			}

			// Confirmed orders are visible to the customer, so their raw Zibal response goes to the error log instead.
			if ($order_info['order_status_id'] && $with_response) {
				$this->log->write('Zibal order_id ' . $order_info['order_id'] . ': ' . str_replace(array("\r", "\n"), ' | ', $comment));

				$comment = $note . "\n" . $this->language->get('text_note_log');
			}

			if (!$order_status_id) {
				$order_status_id = $this->config->get('zibal_failed_status_id') ? $this->config->get('zibal_failed_status_id') : $this->config->get('config_order_status_id');
			}

			// Order history comments are printed without escaping in OpenCart.
			$this->model_payment_zibal->addHistoryNote($order_info['order_id'], $order_status_id, htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'));
		}

		private function format_response() {
			$text = 'HTTP: ' . $this->last_response['http_code'];

			if ($this->last_response['error']) {
				$text .= "\n" . 'cURL error: ' . $this->last_response['error'];
			}

			$text .= "\n" . 'Zibal response: ' . substr($this->last_response['body'], 0, 2000);

			return $text;
		}

		// The browser's return parameters, for notes where Zibal was not called.
		private function format_params() {
			$params = array();

			foreach (array('success', 'status', 'trackId', 'orderId') as $key) {
				$value = isset($this->request->get[$key]) ? $this->request->get[$key] : (isset($this->request->post[$key]) ? $this->request->post[$key] : '');

				$params[] = $key . '=' . (is_string($value) ? html_entity_decode(substr($value, 0, 100), ENT_QUOTES, 'UTF-8') : '?');
			}

			return 'Return params: ' . implode(' & ', $params);
		}

		private function get_price_in_rail($order_info) {
			$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			$currency = $order_info['currency_code'];
			$rate = 0;
			if ($currency == 'RLS' || $currency == 'IRR') {
				$rate = 1;
			} elseif ($currency == 'TOM' || $currency == 'IRT') {
				$rate = 10;
			}
			return (int)round($amount * $rate);
		}

		private function get_token($order_id) {
			return hash_hmac('sha256', 'zibal|' . (int)$order_id, (string)$this->config->get('config_encryption'));
		}

		private function check_token($order_id, $token) {
			if (!is_string($order_id) || !is_string($token) || !ctype_digit($order_id) || !$this->config->get('config_encryption')) {
				return false;
			}

			$known = $this->get_token($order_id);

			if (function_exists('hash_equals')) {
				return hash_equals($known, $token);
			}

			if (strlen($known) != strlen($token)) {
				return false;
			}

			$diff = 0;

			for ($i = 0; $i < strlen($known); $i++) {
				$diff |= ord($known[$i]) ^ ord($token[$i]);
			}

			return $diff === 0;
		}


	}
