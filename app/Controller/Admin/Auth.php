<?php

class Controller_Admin_Auth extends Controller_Default
{

    protected $_isPublic = true;

    public function login()
    {
        $cookieAuth = 'admin_' . \Flannel\Core\Config::get('avidbase.account_id');

        $content = new View_Base('/admin/auth/login.phtml');

        if (!empty(\Input::find('email')) && !empty(\Input::find('password'))) {
            $result = Auth::validateLogin(\Input::find('email'), \Input::find('password'));
            if ($result === true) {
                $this->redirect('/admin');
            } else {
                $content->errorMsg = $result;
            }
        }

        // Destory any existing login
        if ($cookie = Cookie::get($cookieAuth)) {
            Cookie::delete($cookieAuth);
        }

        self::$template->content = $content;

        return;
    }

    public function logout()
    {
        $cookieAuth = 'admin_' . \Flannel\Core\Config::get('avidbase.account_id');

        // The cookie doesn't exist
        if ($cookie = Cookie::get($cookieAuth)) {
            // Decode the access token
            $data = \Flannel\Core\JWT::decode($cookie);
            $userInfo = \Flannel\Core\Session::get($data->u, \Flannel\Core\Session::NAMESPACE_INTERNAL);
            if (!empty($userInfo) && $userInfo->user['id'] && $userInfo->access_key != $data->ak) {
                Helper_Session::Destroy();
            }
        }
        Cookie::delete($cookieAuth);
        $this->redirect('/admin/auth/login');
    }

}
