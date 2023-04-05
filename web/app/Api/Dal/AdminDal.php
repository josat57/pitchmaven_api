<?php

namespace PitchMaven\Api\Dal;

/**
 * The connection class, this file is configured to connect to the database
 *
 * PHP version 8.1.6
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Joseph Samuel <josephsamuelw1@gmail.com>
 * @license  MIT license
 * @link     https://pitchmaven.bootqlass.coms.test
 */

use PitchMaven\Api\Utility;
use PitchMaven\Data\DataOperations;

/**
 * acelinks Acelinks class holds all attributes of the Acelinks functionalities.
 * acelinks Acelinks class holds all attributes of the Acelinks functionalities from
 * sign up to email verification and session management.
 *
 * PHP Version 8.1.6
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Joseph Samuel <josephsamuelw1@gmail.com>
 * @license  MIT license
 * @link     https://pitchmaven.bootqlass.coms.test
 */
class AdminDal extends DataOperations
{
    private static $_input_data;
    private static $_input_file;
    private $_host_url = null;
    private static $_utility = null;
    private const BAD_REQUEST = "HTTP/1.0 400 Bad Request";

    /**
     * Class constructor
     *
     * @param array $data An array of user input data
     *
     * @return array
     */
    public function __construct(array $data = null, array $files = null)
    {
        if (!is_null($data)) {
            unset($data['action']);
            self::$_input_data = $data;
        }

        if (!is_null($files)) {
            self::$_input_file = $files;
        }

        $this->_host_url = $_SERVER['HTTP_ORIGIN']."/frontend";

        self::$_utility = new Utility();
    }

    /**
     * Player Profile method.
     * Creates a player profile based on required values.
     *
     * @return array
     */
    public static function addNewPlayerDetails()
    {
        static::$table = "al_players_tbl";
        static::$pk = "id";
        $upload = null;
        
        if (empty($_SERVER['HTTP_AUTHORIZATION']) || ! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            exit(header(self::BAD_REQUEST));           
        }
        
        $jwt = $matches[1];

        if (empty($matches) || empty($jwt)) {
            exit(header(self::BAD_REQUEST));
        }

        $item = explode('_', base64_decode($jwt));
        
        $verify_jwt = self::$_utility->decodeJWTToken($item[3], $item[0]);

        $response = array();
        self::$_input_data['id'] = $verify_jwt->userName;
        if ($verify_jwt->valid) {
            if (self::getConnection()) {
                                          
                $dir = '/player_profiles';
                if (!empty(self::$_input_file['profile_photo']['name'])) {
                    $upload = self::$_utility->uploadImg(self::$_input_data, self::$_input_file, $dir);
                } else {
                    $upload['statuscode'] = 200;
                }
                
                if ($upload['statuscode'] === 200) {
                    // self::$_input_data['id'] = $verify_jwt->userName;
                    self::$_input_data['modified'] = date('now');
    
                    if (!empty(self::$_input_file['profile_photo']['name'])) {
                        self::$_input_data['profile_photo'] = $upload['filename'];
                        self::$_input_data['profile_path'] = $upload['target_dir'];
                        unset(self::$_input_data['avatar_remove']);
                    }                
                    $result = self::save(self::$_input_data);
    
                    if ($result) {
                        $response = ['statuscode' => 0, 'status' => 'Player profile created successfully'];
                    } else {
                        $response = ['statuscode' => -1, 'status' => 'Could not create player profile'];
                    }
                } else {
                    $response = ['statuscode' => -1, 'status' => "Unable to upload profile picture"];
                }            
            } else {
                $response = ['statuscode' => -1, 'status' => "Connection error"];
            } 
        } else {
            $response = ['statuscode' => -1, 'status' => 'Your session has expired'];
        }    
        return $response;
    }
}