<?php

namespace PitchMaven\Api;
/**
 * PitchMaven Payment Business Logic Layer class
 * Business Logic Layer Class
 * 
 * PHP Version 8.1.3
 * 
 * @category Web_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */

use PitchMaven\Api\Dal\PaymentDal;
use GUMP;

require_once __DIR__."/JWTConfig.php";

/**
 * PitchMaven Payment Business Logic Layer class.
 * Business Logic Layer BLL
 * 
 * PHP Version 8.1.3
 * 
 * @category Web_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */
class PaymentBll
{

    private static $_input_data;
    private static $_input_file;

    /**
     * Class constructor
     * 
     * @param array $data  An array of user input data
     * @param array $files user uploaded file.
     * 
     * @return array
     */
    public function __construct(array $data = null)
    {        
        if (!is_null($data)) {
            unset($data['action']);
            self::$_input_data = $data;
        }                
    }

    /**
     * Get current user method
     * Gets details of current user using the system
     * 
     * @return array
     */
    public function generatePayRef()
    {
        $items = new PaymentDal();
        return $items->generateRef();
    }

    /**
     * Get gift items method.
     * Gets details of all gift items available.
     * 
     * @return array
     */
    public function processPayment()
    {
        $validation = self::_validateInputData(self::$_input_data);
        if ($validation['error']) {
            $response = ['statuscode' => -1, 'status' => $validation['errormsg']];
        } else {
            $data = $validation['my_post'];
            $result = new PaymentDal($data);
            $response = $result->ProcessPayments();
        }
        return $response;
    }
    
    /**
     * Process payment response Method.
     * Process the response from initiated payment processing.
     * 
     * @return mix
     */
    public function verifyPayment()
    {
        $validation = self::_validateVerificationData(self::$_input_data);        
        if ($validation['error']) {
            $response = ['statuscode' => -1, 'status' => $validation['errormsg']];
        } else {
            $data = $validation['my_post'];
            $result = new PaymentDal($data);
            $response = $result->verifyPayments();
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

        $validator = new GUMP;
        $rules = null;   
        $myPost = $data;

        $myPost = $validator->sanitize($myPost);

        $filters = array(
            'first_name' => 'trim|sanitize_string',
            'last_name' => 'trim|sanitize_string',
            'email' => 'trim|sanitize_string',
            'mobile' => 'trim|sanitize_string',
            'amount' => 'trim|sanitize_string',
            'currency' => 'trim|sanitize_string',
            'country' => 'trim|sanitize_string',
            'password' => 'trim|sanitize_string',
            'confirm_password' => 'trim|sanitize_string',
        );
        $myPost = $validator->filter($myPost, $filters);
        
        $rules = array(
            'first_name' => 'required|min_len,3|max_len,200',
            'last_name' => 'required|min_len,3|max_len,200',
            'email' => 'required|min_len,3|max_len,200',
            'mobile' => 'required|min_len,8|max_len,14',
            'amount' => 'required|numeric',
            'currency' => 'required|min_len,2',
            'country' => 'required|min_len,2',
            'password' => 'required|min_len,6|max_len,25',
        );
               

        $validated = $validator->validate($myPost, $rules);
        if ($validated === true) {
            $return = array(
                'post' => $data,
                'error' => false,
                'errormsg' => "",
                'my_post' => $myPost
            );
        } else {
            $return = array(
                'post' => $data,
                'error' => true,
                'errormsg' => $validator->get_readable_errors(),
                'my_post' => $myPost
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
    private function _validateVerificationData($dataSet)
    {
        $data = (array) $dataSet;   

        $validator = new GUMP;
        $rules = null;   
        $myPost = $data;

        $myPost = $validator->sanitize($myPost);

        $filters = array(
            'transaction_id' => 'trim|sanitize_string',
            'status' => 'trim|sanitize_string',
            'tx_ref' => 'trim|sanitize_string'
        );
        $myPost = $validator->filter($myPost, $filters);
        
        $rules = array(
            'transaction_id' => 'required|min_len,3|max_len,200',
            'status' => 'required|min_len,3|max_len,200',
            'tx_ref' => 'required|min_len,3|max_len,200'
        );
               

        $validated = $validator->validate($myPost, $rules);
        if ($validated === true) {
            $return = array(
                'post' => $data,
                'error' => false,
                'errormsg' => "",
                'my_post' => $myPost
            );
        } else {
            $return = array(
                'post' => $data,
                'error' => true,
                'errormsg' => $validator->get_readable_errors(),
                'my_post' => $myPost
            );
        }
        return $return;
    }
}