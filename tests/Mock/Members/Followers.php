<?php

namespace Mock\Members;

class Followers extends \Yarest\Resource
{
    public function all($id)
    {
        echo "Member ($id) Followers all";
    }
}
