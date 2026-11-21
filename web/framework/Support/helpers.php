<?php

//	**************************************************
//	                     CSRF
//	**************************************************

if (! function_exists('csrf_token'))
{
    function csrf_token()
    {
    	echo e(Csrf::token());
    }
}

if (! function_exists('csrf_field'))
{
    function csrf_field()
    {
        Csrf::field();
    }
}

//	**************************************************
//	                     VIEW
//	**************************************************

if (! function_exists('view'))
{
    function view($path)
    {
    	View::render($path);
    }
}

if (! function_exists('e'))
{
    /** Escape a value for HTML output */
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('money'))
{
    /** 2400 -> "2 400 ₸" */
    function money($value)
    {
        return number_format((float) $value, 0, '', ' ') . ' ₸';
    }
}

if (! function_exists('icon'))
{
    /** Inline SVG icon, see Icons::$paths for available names */
    function icon($name, $class = '')
    {
        return Icons::svg($name, $class);
    }
}

//	**************************************************
//	                  REQUEST / RESPONSE
//	**************************************************

if (! function_exists('json_response'))
{
    function json_response(array $data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (! function_exists('redirect'))
{
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (! function_exists('safe_path'))
{
    /** Allow only local paths like "/cabinet" to prevent open redirects */
    function safe_path($path, $default = '/')
    {
        return (is_string($path) && preg_match('#^/(?!/)[\w\-/]*$#', $path)) ? $path : $default;
    }
}

//	**************************************************
//	                     AUTH
//	**************************************************

if (! function_exists('session_is'))
{
    function session_is($name)
    {
        return Session::is($name);
    }
}

if (! function_exists('auth_user'))
{
    /** Current user as an array or null */
    function auth_user($field = null)
    {
        return Session::get('user', $field);
    }
}

if (! function_exists('is_admin'))
{
    function is_admin()
    {
        return auth_user('role') === 'admin';
    }
}

if (! function_exists('login_user'))
{
    function login_user($user)
    {
        session_regenerate_id(true);
        Session::set('user', [
            'id'    => (int) $user->id,
            'login' => $user->login,
            'email' => $user->email,
            'role'  => $user->role
        ]);
    }
}
