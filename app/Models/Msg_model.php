<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class Msg_model extends Model
{

    public function __construct(ConnectionInterface &$db){
        $this->db = $db;
    }

    public function get_msgs($conditions){
        $builder = $this->db->table('messages');
        $builder->select('messages.id, messages.reply,messages.reply_from,messages.created_at,messages.reply_type,messages_type.type');
        $builder->join('messages_type', 'messages.reply_type = messages_type.id');
        $builder->join('chats', 'messages.chat_id = chats.id');
        $builder->where($conditions);
        $builder->orderBy('messages.id', 'desc');
        $query = $builder->get();
        return $query->getResult();
    }

}