<?php

    function getLoggedInUserId()
    {
        $id = '';
        $sessionId = TinyPHP_Session::get('userId'); 
        if(isset($sessionId))
        {
            if(is_numeric($sessionId))
            {
                $id = TinyPHP_Session::get('userId');
            }
 
        }
        return $id;
    }

    function getLoggedInUser()
    {
        $id = getLoggedInUserId();
        $obj = new Models_User($id);
        return $obj; 
    }

    function getLoggedInAdminId()
    {
        $id = '';
        $sessionId = TinyPHP_Session::get('adminId'); 
        if(isset($sessionId))
        {
            if(is_numeric($sessionId))
            {
                $id = TinyPHP_Session::get('adminId');
            }
 
        }
        return $id;
    }

    function getLoggedInAdmin()
    {
        $id = getLoggedInAdminId();
        $obj = new Models_User($id);

        return $obj; 
    }


?>