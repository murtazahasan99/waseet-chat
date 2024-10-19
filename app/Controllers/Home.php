<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Crud_model;
use App\Models\Msg_model;

class Home extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        helper(['security_helper', 'api_helper', 'check_auth_helper']);
    }

    public function index()
    {
        if (!checkAuth('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return view('chat/home');
    }

    public function login()
    {
        if (checkAuth('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('chat/login');
    }

    public function postLogin()
    {
        $username = sanitize_input($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        $db = $db = \Config\Database::connect("waseet");
        $crud_model = new Crud_model($db);

        if ($user = $crud_model->login('ms_users', $username, $password)) {

            $session = session();
            $session->set('username', $username);
            $session->set('name', $user->name);
            $session->set('user_id', $user->id);
            $session->set('mobile', $user->mobile);
            $session->set('city_id', $user->city_id);
            $session->set('token', $user->token);
            $session->set('isLoggedIn', true);

            return $this->respond(responsBuilder('تم تسجيل الدخول بنجاح', true));
        }

        return $this->respond(responsBuilder('اسم المستخدم او كلمة المرور غير صحيحة', false), 401);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }


    public function getGeneralChats()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $chats = $crud_model->find_all('chats', ["is_opened" => 0, "merchant_city_id" => session()->get('city_id')], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at");

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $chats), 200);

    }

    public function getPersonalChats()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $user_id = session()->get('user_id');
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $chats = $crud_model->find_all('chats', ["is_opened" => 1 ,"is_rated" => 0 , "ms_id" =>$user_id ], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at");

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $chats), 200);

    }

    public function getArchivedChats() 
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $user_id = session()->get('user_id');
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $chats = $crud_model->find_all('chats', ["is_closed" => 1, "is_rated" => 1, "ms_id" => $user_id], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at");

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $chats), 200);
    }

    public function getChatMsgs()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $msg_model = new Msg_model($db);

        $chat_id = sanitize_input($this->request->getVar('chat_id'));

        if (empty($chat_id)) {
            return $this->respond(responsBuilder('الرجاء تحديد المحادثة', false), 400);
        }

        $condtions = [
            "chat_id" => $chat_id,
            "chats.ms_id" => session()->get('user_id')
        ];

        $msgs = $msg_model->get_msgs($condtions);

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $msgs), 200);

    }

}
