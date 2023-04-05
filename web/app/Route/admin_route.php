<?php

/**
 * Authentication route
 * PHP Version 8.1.3
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */

use PitchMaven\Api\AdminBll;

error_reporting(E_ALL);

/**
 * Consultant function allows requests that are for this route only
 *
 * @param object $data input data object
 *
 * @return json
 */
function adminRouter($data)
{
    $auth = new AdminBll($data);

    switch ($data['action']) {
        
    case "add_player_admin":
        $response = $auth->addNewPlayer();
        break;
        
    default:
        $response = ['statuscode'=>-1, "status"=>"Invalid request action"];
        break;
    }
    return $response;
}
