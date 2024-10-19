<?php

namespace App\Controllers\merchant;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Crud_model;
use App\Models\Msg_model;

class Api extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        helper(['security_helper', 'api_helper', 'check_auth_helper']);
    }

    public function init()
    {
        //check if isset token
        $token = sanitize_input($this->request->getVar('token'));
        if (!$user_id = $this->checkToken($token)) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }

        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $saved_replys = $crud_model->find_all('saved_replys', ['status_id' => 1]);
        $messages_type = $crud_model->find_all('messages_type', []);
        $archive_chats = $crud_model->find_all('chats', ["merchant_id" => $user_id, "is_closed" => 1, "is_rated" => 1], "id,last_reply,last_reply_from,last_reply_type,updated_at,created_at");
        $active_chats = $crud_model->find_all('chats', ["merchant_id" => $user_id, "is_rated" => 0], "id,last_reply,last_reply_from,last_reply_type,updated_at,created_at");

        $data = [
            "saved_replys" => $saved_replys,
            "messages_type" => $messages_type,
            "archive_chats" => $archive_chats,
            "active_chats" => $active_chats
        ];
        return $this->respond(responsBuilder('تم العملية بنجاح', true, $data), 200);
    }

    public function get_chat_msgs()
    {
        //check if isset token
        $token = sanitize_input($this->request->getVar('token'));
        if (!$user_id = $this->checkToken($token)) {
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
            "chats.merchant_id" => $user_id
        ];

        $msgs = $msg_model->get_msgs($condtions);

        return $this->respond(responsBuilder('تم العملية بنجاح', true, $msgs), 200);
    }

    public function open_new_chat()
    {
        //check if isset token
        $token = sanitize_input($this->request->getVar('token'));
        if (!$user_id = $this->checkToken($token)) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }

        $db = $db = \Config\Database::connect("waseet");
        $crud_model = new Crud_model($db);
        
        $merchant = $crud_model->find_one('merchant', ["id" => $user_id]);


        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $saved_reply_id = sanitize_input($this->request->getVar('saved_reply_id'));

        $saved_reply = $crud_model->find_one('saved_replys', ["id" => $saved_reply_id]);

        if (empty($saved_reply)) {
            return $this->respond(responsBuilder('الرجاء قم باختيار الرد المحفوظ', false), 400);
        }
        $active_chats = $crud_model->find_all('chats', ["merchant_id" => $user_id, "is_closed" => 0, "is_rated" => 0], "id,last_reply,last_reply_from,updated_at");

        if (!empty($active_chats)) {
            return $this->respond(responsBuilder("لا يمكنك فتح دردشة جديدة لديك دردشة نشطة قم باغلاقها اولا", false), 400);
        }
        $data = [
            "merchant_id" => $user_id,
            "merchant_city_id" => $merchant->city_id,
            "merchant_name" => $merchant->name,
            "merchant_username" => $merchant->username,
            "last_reply" => $saved_reply->reply,
            "last_reply_from" => "merchant",
            "saved_reply_id" => $saved_reply_id,
            "last_reply_type" => 1, // text message
            "updated_user_id" => $user_id,
            "updated_user_table" => "merchant"
        ];

        if ($chat_id = $crud_model->create('chats', $data)) {
            $msg = array(
                "reply" => $saved_reply->reply,
                "reply_from" => "merchant",
                "chat_id" => $chat_id,
                "reply_type" => 1, // text message
            );
            $crud_model->create('messages', $msg);

            return $this->respond(responsBuilder('تم العملية بنجاح', true, $chat_id), 200);
        } else {
            return $this->respond(responsBuilder('حدث خطاء في الشبكة الرجاء المحاولة مرة اخرى', false), 400);
        }
    }

    public function add_reply()
    {
        //check if isset token
        $token = sanitize_input($this->request->getVar('token'));
        if (!$user_id = $this->checkToken($token)) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }
        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $reply = sanitize_input($this->request->getVar('reply'));
        $reply_type = sanitize_input($this->request->getVar('reply_type'));

        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "merchant_id" => $user_id, "is_closed" => 0, "is_rated" => 0]);

        if (empty($chat)) {
            return $this->respond(responsBuilder('لا توجد محادثة نشطه لهذا المعرف', false), 400);
        }
        if (empty($reply)) {
            return $this->respond(responsBuilder('الرجاء كتابة الرد', false), 400);
        }
        if (empty($reply_type)) {
            $reply_type = 1; // text message
        }

        $msg = array(
            "reply" => $reply,
            "reply_from" => "merchant",
            "chat_id" => $chat_id,
            "reply_type" => $reply_type,
        );

        if ($crud_model->create('messages', $msg)) {
            $data = [
                "last_reply" => $reply,
                "last_reply_from" => "merchant",
                "last_reply_type" => $reply_type,
                "updated_user_id" => $user_id,
                "updated_user_table" => "merchant"
            ];
            $crud_model->edit('chats', $data, ["id" => $chat_id]);


            return $this->respond(responsBuilder('تم العملية بنجاح', true, $chat_id), 200);
        } else {
            return $this->respond(responsBuilder('حدث خطاء في الشبكة الرجاء المحاولة مرة اخرى', false), 400);
        }
    }

    public function rate_chat()
    {
        //check if isset token
        $token = sanitize_input($this->request->getVar('token'));
        if (!$user_id = $this->checkToken($token)) {
            return $this->respond(responsBuilder('الرجاء تسجيل الدخول', false), 401);
        }

        $db = $db = \Config\Database::connect("default");
        $crud_model = new Crud_model($db);

        $chat_id = sanitize_input($this->request->getVar('chat_id'));
        $rate = sanitize_input($this->request->getVar('rate'));
        $rate_notes = sanitize_input($this->request->getVar('rate_notes'));

        if (empty($rate) || $rate < 1 || $rate > 5) {
            return $this->respond(responsBuilder('الرجاء تقييم المحادثة بين 1 و 5', false), 400);
        }

        $chat = $crud_model->find_one('chats', ["id" => $chat_id, "merchant_id" => $user_id, "is_closed" => 1, "is_rated" => 0]);

        if (empty($chat)) {
            return $this->respond(responsBuilder('لا يمكنك تقييم هذه الدردشة بالوقت الحالي', false), 400);
        }
        $data = [
            "is_rated" => 1,
            "rate" => $rate,
            "rate_notes" => $rate_notes,
            "rated_at" => date("Y-m-d H:i:s"),
            "rated_date" => date("Y-m-d"),
            "updated_user_id" => $user_id,
            "updated_user_table" => "merchant"

        ];
        if ($crud_model->edit('chats', $data, ["id" => $chat_id, "merchant_id" => $user_id, "is_closed" => 1, "is_rated" => 0])) {
            return $this->respond(responsBuilder('تم التقييم بنجاح بنجاح', true), 200);
        } else {
            return $this->respond(responsBuilder('حدث خطاء في الشبكة الرجاء المحاولة مرة اخرى', false), 400);
        }
    }

    private function checkToken($token)
    {
        $db = $db = \Config\Database::connect("waseet");

        $crud_model = new Crud_model($db);
        if (strpos($token, "@@") > -1) {
            if ($user = $crud_model->checkToken("merchant_users", ["token" => $token])) {
                return $user->merchant_id;
            } else {
                return false;
            }
        } else {
            if ($user = $crud_model->checkToken("merchant", ["token" => $token])) {
                return $user->id;
            } else {
                return false;
            }
        }
    }
}
