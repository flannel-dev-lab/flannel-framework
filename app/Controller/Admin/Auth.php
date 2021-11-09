<?php

class Controller_Admin_Auth extends Controller_Default {

    public function login() {

        self::$template->content = new View_Base('/admin/auth/login.phtml');

        return;
    }

}
