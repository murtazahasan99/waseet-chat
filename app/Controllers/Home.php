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
        helper(['security_helper', 'api_helper', 'check_auth_helper','form']);
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

        $chats = $crud_model->find_all('chats', ["is_opened" => 0, "merchant_city_id" => session()->get('city_id')], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at,ms_id,is_closed,is_rated");

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

        $chats = $crud_model->find_all('chats', ["is_opened" => 1 ,"is_rated" => 0 , "ms_id" =>$user_id ], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at,ms_id,is_closed,is_rated");

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

        $chats = $crud_model->find_all('chats', ["is_closed" => 1, "is_rated" => 1, "ms_id" => $user_id], "id,merchant_name,merchant_username,last_reply,last_reply_from,last_reply_type,updated_at,created_at,ms_id,is_closed,is_rated");

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
            "chat_id" => $chat_id
        ];

        $msgs = $msg_model->get_msgs($condtions);

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $msgs), 200);

    }

    public function startChat()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);
        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "is_opened" => 0]);
        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكن بدء المحادثة بسبب بدئها من قبل شخص اخر', false), 400);
        }

        $data = [
            "ms_id" => session()->get('user_id'),
            "is_opened" => 1,
            "opened_at" => date('Y-m-d H:i:s'),
            "opened_date" => date('Y-m-d'),
            "updated_user_id" => session()->get('user_id'),
            "updated_user_table" => "ms_users",
        ];
        if($crud_model->edit('chats', $data, ["id" => $chat_id, "is_opened" => 0])){
            return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
        }
        return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
    }

    public function sendMsg()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);
        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $msg = sanitize_input($this->request->getVar('msg'));

        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "ms_id" => session()->get('user_id')]);
        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكنك ارسال رسالة لهذه المحادثة', false), 400);
        }
        $data = [
            "chat_id" => $chat_id,
            "reply" => $msg,
            "reply_from" => "ms_user",
            "reply_type" => 1,
        ];
        if($crud_model->create('messages', $data)){
            $data = [
                "last_reply" => $msg,
                "last_reply_from" => "ms_user",
                "last_reply_type" => 1,
                "updated_user_id" => session()->get('user_id'),
                "updated_user_table" => "ms_users",
            ];

            if($crud_model->edit('chats', $data, ["id" => $chat_id])){
                return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
            }
            return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
        }
        return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
    }

    public function closeChat()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);
        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "is_opened" => 1, "is_closed" => 0, "ms_id" => session()->get('user_id')]);
        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكن اغلاق المحادثة في الوقت الحالي', false), 400);
        }

        $data = [
            "ms_id" => session()->get('user_id'),
            "is_closed" => 1,
            "closed_at" => date('Y-m-d H:i:s'),
            "closed_date" => date('Y-m-d'),
            "updated_user_id" => session()->get('user_id'),
            "updated_user_table" => "ms_users",
        ];
        if($crud_model->edit('chats', $data, ["id" => $chat_id, "ms_id" => session()->get('user_id')])){
            return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
        }
        return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
    }

    public function reopenChat()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);
        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "is_opened" => 1, "is_closed" => 1, "ms_id" => session()->get('user_id'),"is_rated" => 0]);
        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكن اعادة فتح المحادثة في الوقت الحالي', false), 400);
        }

        $data = [
            "ms_id" => session()->get('user_id'),
            "is_closed" => 0,
            "closed_at" => "0000-00-00 00:00:00",
            "closed_date" => "0000-00-00",
            "updated_user_id" => session()->get('user_id'),
            "updated_user_table" => "ms_users",
        ];
        if($crud_model->edit('chats', $data, ["id" => $chat_id, "ms_id" => session()->get('user_id')])){
            return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
        }
        return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
    }
    public function sendImg()
    {
        if (!checkAuth('isLoggedIn')) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);
        $chat_id = sanitize_input($this->request->getVar('chat_id'));

        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "ms_id" => session()->get('user_id')]);
        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكنك ارسال رسالة لهذه المحادثة', false), 400);
        }
        $rules = [
            'img' => [
                'rules' => 'uploaded[img]|mime_in[img,image/jpg,image/jpeg,image/gif,image/png]',
                'errors' => [
                    'uploaded' => 'الرجاء تحميل الصورة',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->respond(responsBuilder('الملف يجب ان يكون صوره مناسبه', false, $this->validator->getErrors()), 400);
        }
        $img = $this->request->getFile('img');
        $img_name = $img->getRandomName();
        $img->move('uploads/', $img_name);
        $msg = 'uploads/' . $img_name;

        $data = [
            "chat_id" => $chat_id,
            "reply" => $msg,
            "reply_from" => "ms_user",
            "reply_type" => 3,
        ];
        if($crud_model->create('messages', $data)){
            $data = [
                "last_reply" => $msg,
                "last_reply_from" => "ms_user",
                "last_reply_type" => 3,
                "updated_user_id" => session()->get('user_id'),
                "updated_user_table" => "ms_users",
            ];

            if($crud_model->edit('chats', $data, ["id" => $chat_id])){
                return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
            }
            return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
        }
        return $this->respond(responsBuilder('حدث خطأ ما', false), 400);
    }

}
