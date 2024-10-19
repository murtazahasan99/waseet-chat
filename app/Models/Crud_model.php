<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class Crud_model extends Model
{

    public function __construct(ConnectionInterface &$db){
        $this->db = $db;
    }

    public function find_one($table ,$conditions, $fields = '*'){

        $builder = $this->db->table($table);
        $builder->select($fields);
        $builder->where($conditions);
        $query = $builder->get();
        return $query->getRow();
    }

    public function find_all($table, $conditions, $fields = '*'){
        $builder = $this->db->table($table);
        $builder->select($fields);
        $builder->where($conditions);
        $query = $builder->get();
        return $query->getResult();
    }

    public function create($table, $data){
        $builder = $this->db->table($table);
        $builder->insert($data);
        return $this->db->insertID();
    }

    public function create_bulk($table, $data){
        $builder = $this->db->table($table);
        if($builder->insertBatch($data)){
            return true;
        }else{
            return false;
        }
    }

    public function edit($table, $data, $conditions){
        $builder = $this->db->table($table);
        $builder->where($conditions);
        $builder->update($data);
        return $this->db->affectedRows();
    }

    public function delete_one($table, $conditions){
        $builder = $this->db->table($table);
        $builder->where($conditions);
        $builder->limit(1);
        $builder->delete();
        return $this->db->affectedRows();
    }

    public function login($table, $username, $password){
        $builder = $this->db->table($table);
        $builder->where(["username" => $username, "status_id" => 1 ]); // status_id = 1 means active
        $query = $builder->get();
        if($query->getNumRows() > 0){
            $user = $query->getRow();
            if(password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;

    }

    public function checkToken($table, $conditions){
        
        $builder = $this->db->table($table);
        $builder->where($conditions);
        $query = $builder->get();
        return $query->getRow();

    }
}