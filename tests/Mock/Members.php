<?php

namespace Mock;

class Members extends \Yarest\Resource
{
    /**
     * @var int      member_id
     * @var string   firstname
     * @var string   lastname
     */

    public function all()
    {
        
        // content type
        // status codes
        // authentiction
        // authorization
        // validation
        // transformation
        // input fields
        // database query
        // output fields
        // post proccessing

        // $members = $this->db->getAll("SELECT ".implode(',', $this->vars)." FROM member Limit 10");

        // $this->response->setBody($members)->toJSON();
        $person = $this['db']->getAll('SELECT * FROM member LIMIT 4');
        return $person;
    }

    /**
     * @var int      member_id
     * @var string   firstname
     * @var string   middlename
     * @var string   lastname
     * @var string   nickname
     * @var datetime created
     * @var datetime updated
     * @var date     birthday
     */
    
    public function get($id)
    {
        $member = $this->db->getOne("SELECT ".implode(',', $this->vars)." FROM member WHERE member_id = ? Limit 1", [$id]);
        
        if ($member) {
            $this->response->setBody($member)->toJSON();
        } else {
            $this->response->setStatus(404, 'Member Not Found');
        }

    }

    public function create()
    {
        $member = $this->db->getOne("SELECT * FROM member WHERE member_id = ? Limit 1", [$id]);
        
        if ($member) {
            $this->response->setStatus(201);
            $this->response->setBody($member)->toJSON();
        } else {
            $this->response->setStatus(400);
        }

    }

    public function recover($id)
    {
        $member = $this->db->exec("UPDATE member SET deleted=false WHERE member_id = ?", [$id]);
        if ($member) {
            return json_encode($member);
        } else {
            header('HTTP/1.1 404 Member Not Found');
        }
    }

    public function remove($id)
    {
        $member = $this->db->exec("UPDATE member SET deleted=true WHERE member_id = ?", [$id]);
        if ($member) {
            return json_encode($member);
        } else {
            header('HTTP/1.1 404 Member Not Found');
        }
    }
}
