<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH . 'controllers/api/REST_Controller.php');
require(APPPATH . 'controllers/api/RSA.php');


class Tenant_api extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Multitenant_model');
        $this->load->model('Rest_keys_model');
    }

    public function user_post()
    {

        $result = $this->Multitenant_model->read();
        $this->response($result);

    }
    public function initSession_post()
    {
        $this->session->set_userdata("SessionTenant_newLogin", "root");
        $this->response(json_encode($this->input->post('root')));    

    }
    public function ExistTenant_post()
    {
        try {
            if($this->post('key')) {
                $request_data = array();
                $request_data["key"] = "verif key";
                $serialized_request_data = serialize($request_data);
                $enc = new RSAEnc();
                $enc = $enc->result($serialized_request_data, get_setting("api_public_key"));
                $request_data = array("data" => base64_encode($enc->result), "key" => base64_encode($enc->key));
                if (get_setting("enable_api_debug") == "true") {
                    api_debug($request_data);
                }
                $response = api_post("run", $request_data);
            }
        } catch (ErrorException $e) {
            $this->phpError($e);
        }

        $this->session->set_userdata("SessionTenant_newLogin", "root");
        $email = $this->input->post('name');
        $result = $this->Multitenant_model->getTenant($email);
        if ($result[0] == 1) {
            $this->session->set_userdata("SessionTenant_newLogin", $result[1][0]);
        }

        $this->response(json_encode($result));
    }

    public function selectedTenant_post()
    {
        $this->session->set_userdata("SessionTenant_newLogin", "root");
        $NameTenant = $this->input->post('Tenant');
        $this->session->set_userdata("SessionTenant_newLogin", $NameTenant);
        $result = "success";
        $this->response($result);
    }

}
