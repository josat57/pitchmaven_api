<?php

/** 
 * Authentication route
 * PHP Version 8.1.3
 * 
 * @category MicroService_API_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */

use PitchMaven\Api\UsersBll;

error_reporting(E_ALL);

// require_once __DIR__."/../api/auth.bll.php";

/**
 * Consultant function allows requests that are for this route only
 * 
 * @param array $data input data object
 * 
 * @return json
 */
function usersRouter(array $data = null)
{ 
    $file = null;
    if (isset($_FILES)) {
        $file = $_FILES;
    }
    $user_object = new UsersBll($data, $file);

    switch($data['action']) {
        
    case "get_current_users":
        $response = $user_object->getUserProfile();
        break;    
        
    case "upload_item_users":
        $response = $user_object->giveOutItems();
        break;    
        
    case "add_categories_users":
        $response = $user_object->addCategories();
        break;    
        
    case "update_profile_users":
        $response = $user_object->updateUserProfile();
        break;

        
    case "update_email_users":
        $response = $user_object->updateUserEmail();
        break;
            
    default:
        $response = ['statuscode'=>0, "status"=>"Invalid request action"];
        break;
    }
    return $response;
} 
