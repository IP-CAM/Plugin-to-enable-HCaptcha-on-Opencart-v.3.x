<?php
class ControllerExtensionCaptchaHCaptcha extends Controller {
    public function index($error = array()) {
        $this->load->language('extension/captcha/hcaptcha');

        if (isset($error['captcha'])) {
			$data['error_captcha'] = $error['captcha'];
		} else {
			$data['error_captcha'] = '';
		}

		$data['site_key'] = $this->config->get('captcha_hcaptcha_key');

	        $data['route'] = $this->request->get['route']; 

		return $this->load->view('extension/captcha/hcaptcha', $data);
    }

    public function validate() {
		if (empty($this->session->data['hcaptcha'])) {
			$this->load->language('extension/captcha/hcaptcha');

			if (!isset($this->request->post['h-captcha-response'])) {
				return $this->language->get('error_captcha');
			}

			$data = array(
						'secret' => $this->config->get('captcha_hcaptcha_secret'),
						'response' => $_POST['h-captcha-response']
					);
			$verify = curl_init();
			curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
			curl_setopt($verify, CURLOPT_POST, true);
			curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($verify);
			// var_dump($response);
			$responseData = json_decode($response);
			if($responseData->success) {
				// your success code goes here
				$this->session->data['hcaptcha']	= true;
			} 
			else {
			   // return error to user; they did not pass
			   return $this->language->get('error_captcha');
			}

		}
    }
}
