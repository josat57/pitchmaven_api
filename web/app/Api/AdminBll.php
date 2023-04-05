<?php


namespace PitchMaven\Api;

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

use PitchMaven\Api\Dal\AdminDal;
use GUMP\GUMP;

/**
 * PitchMaven class holds all attributes of the PitchMaven functionalities.
 *
 * PHP Version 8.1.6
 *
 * @category Micro_Service_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Joseph Samuel <josephsamuelw1@gmail.com>
 * @license  MIT license
 * @link     https://pitchmaven.bootqlass.coms.test
 */
class AdminBll
{
    private static $_input_data;
    /**
     * Class constructor
     *
     * @param array $data An array of user input data
     *
     * @return array
     */
    public function __construct($data = null)
    {
        if (!is_null($data)) {
            unset($data['action']);
            self::$_input_data = $data;
        }
        // ceil(8.0);
    }



    public function addNewPlayer()
    {
        $validated = $this->_validateInputData((array) self::$_input_data);
        if ($validated['error']) {
            $response = ['statuscode' => -1, 'status' => $validated['errormsg']];
        } else {
            $data = $validated['my_post'];
            $auth = new AdminDal($data);
            $response = $auth->addNewPlayerDetails();
        }
        return $response;
    }


    /**
     * Validate input data method
     * Validates input data from user to create account
     *
     * @param object $dataSet input data to validate
     *
     * @return object
     */
    private function _validateInputData($dataSet)
    {
        $data = (array) $dataSet;

        $validator = new \GUMP;
        $rules = null;
        $mypost = $data;

        $mypost = $validator->sanitize($mypost);

        $filters = array(
            'first_name' => 'trim|sanitize_string',
            'last_name' => 'trim|sanitize_string',
            'other_name' => 'trim|sanitize_string',
            'guardian_first_name' => 'trim|sanitize_string',
            'guardian_last_name' => 'trim|sanitize_string',
            'guardian_address' => 'trim|sanitize_string',
            'guardian_phone' => 'trim|sanitize_string',
            'email' => 'trim|sanitize_string',
            'role' => 'trim|sanitize_string',
            'dob' => 'trim|sanitize_string',
            'street' => 'trim|sanitize_string',
            'weight' => 'trim|sanitize_string',
            'height' => 'trim|sanitize_string',
            'team_id' => 'trim|sanitize_string',
            'feet_orientation' => 'trim|sanitize_string',
            'position' => 'trim|sanitize_string',
            'city' => 'trim|sanitize_string',
            'state' => 'trim|sanitize_string',
            'country' => 'trim|sanitize_string',
            'place_of_birth' => 'trim|sanitize_string',
            'marital_status' => 'trim|sanitize_string',
            'gender' => 'trim|sanitize_string',
            'mobile' => 'trim|sanitize_string',
            'user_name' => 'trim|sanitize_string',
            'password' => 'trim|sanitize_string'
        );
        $mypost = $validator->filter($mypost, $filters);
        
        $rules = array(
            'first_name' => 'required|min_len,3|max_len,200',
            'last_name' => 'required|min_len,3|max_len,200',
            'other_name' => 'min_len,0|max_len,200',
            'guardian_first_name' => 'required|min_len,3|max_len,200',
            'guardian_last_name' => 'required|min_len,3|max_len,200',
            'guardian_address' => 'required|min_len,3|max_len,200',
            'guardian_phone' => 'required|min_len,3|max_len,200',
            'email' => 'required|min_len,3|max_len,200',
            'role' => 'required|min_len,3|max_len,200',
            'dob' => 'required|min_len,3|max_len,200',
            'street' => 'required|min_len,3|max_len,200',
            'weight' => 'min_len,0|max_len,200',
            'height' => 'min_len,0|max_len,200',
            'team_id' => 'min_len,1|max_len,200',
            'feet_orientation' => 'required|min_len,3|max_len,200',
            'position' => 'required|min_len,2|max_len,200',
            'city' => 'required|min_len,3|max_len,200',
            'state' => 'required|min_len,3|max_len,200',
            'country' => 'required|min_len,2|max_len,200',
            'place_of_birth' => 'required|min_len,3|max_len,200',
            'marital_status' => 'required|min_len,3|max_len,200',
            'gender' => 'required|min_len,3|max_len,200',
            'mobile' => 'required|min_len,3|max_len,200',
            'user_name' => 'min_len,5|max_len,200',
            'password' => 'min_len,8|max_len,200',
        );
               

        $validated = $validator->validate($mypost, $rules);
        if ($validated === true) {
            $return = array(
                'post' => $data,
                'error' => false,
                'errormsg' => "",
                'my_post' => $mypost
            );
        } else {
            $return = array(
                'post' => $data,
                'error' => true,
                'errormsg' => $validator->get_readable_errors(),
                'my_post' => $mypost
            );
        }
        return $return;
    }

    /**
     * Validate input data method
     * Validates input data from user to create account
     *
     * @param object $dataSet input data to validate
     *
     * @return object
     */
    private function _validateSignIn($dataSet)
    {
        $data = (array) $dataSet;

        $validator = new \GUMP;
        $rules = null;
        $mypost = $data;

        $mypost = $validator->sanitize($mypost);

        $filters = array(
            'email' => 'trim|sanitize_string',
            'password' => 'trim|sanitize_string',
        );
        $mypost = $validator->filter($mypost, $filters);
        
        $rules = array(
            'email' => 'required|min_len,10',
            'password' => 'required|min_len,6',
        );
               

        $validated = $validator->validate($mypost, $rules);
        if ($validated === true) {
            $return = array(
                'post' => $data,
                'error' => false,
                'errormsg' => "",
                'mypost' => $mypost
            );
        } else {
            $return = array(
                'post' => $data,
                'error' => true,
                'errormsg' => $validator->get_readable_errors(),
                'mypost' => $mypost
            );
        }
        return $return;
    }

    /**
     * Validate input data method
     * Validates input data from user to create account
     *
     * @param array $dataSet input data to validate
     *
     * @return object
     */
    private function _validateCode($dataSet)
    {
        $validator = new \GUMP;
        $rules = null;
        $mypost = $dataSet;
        
        $mypost = $validator->sanitize($mypost);

        $filters = array(
            'confirm_code' => 'trim|sanitize_string',
            'new_password' => 'trim|sanitize_string',
            'confirm_password' => 'trim|sanitize_string',
        );
        $mypost = $validator->filter($mypost, $filters);
        
        $rules = array(
            'confirm_code' => 'required|min_len,3',
            'new_password' => 'required|min_len,6',
            'confirm_password' => 'required|min_len,6',
        );
               

        $validated = $validator->validate($mypost, $rules);
        if ($validated === true) {
            $return = array(
                'post' => $dataSet,
                'error' => false,
                'errormsg' => "",
                'mypost' => $mypost
            );
        } else {
            $return = array(
                'post' => $dataSet,
                'error' => true,
                'errormsg' => $validator->get_readable_errors(),
                'mypost' => $mypost
            );
        }
        return $return;
    }
}