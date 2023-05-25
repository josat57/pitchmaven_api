<?php

/**
 * Authentication route
 * PHP Version 8.1.3
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.coms.test
 */

use PitchMaven\Api\AuthBll;

error_reporting(E_ALL);

/**
 * Consultant function allows requests that are for this route only
 *
 * @param object $data input data object
 *
 * @return json
 */
function authRouter($data)
{
    $auth = new AuthBll($data);
    switch ($data['action']) {
        
    case "get_token_auth":
        $response = $auth->requestSetToken();
        break;

    case "sign_up_auth":
        $response = $auth->signUpUsers();
        break;

    case "sign_in_auth":
        $response = $auth->signInUsers();
        break;

    case "sign_out_auth":
        $response = $auth->signOutUser();
        break;

    case "verify_session_auth":
        // $response = $auth->verifySession();
        break;

    case "change_password_auth":
        $response = $auth->changePassword();
        break;

    case "reset_password_auth":
        $response = $auth->resetUserPassword();
        break;

    case "delete_account_auth":
        $response = $auth->deleteAccount();
        break;

    case "lock_account_auth":
        $response = $auth->lockUser();
        break;

    case "verify_signup_auth":
        $response = $auth->verifyEmail();
        break;

    case "confirm_otp_auth":
        $response = $auth->confirmOtpCode();
        break;
        
    default:
        $response = ['statuscode'=>0, "status"=>"Invalid request action"];
        break;
    }
    return $response;
}
