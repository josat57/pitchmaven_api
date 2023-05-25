<?php

namespace PitchMaven\Api\Dal;
/**
 * PitchMaven Data Access Layer class. 
 * Data Access Layer DAL
 * 
 * PHP Version 8.1.3
 * 
 * @category Web_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmavenapi.bootqlass.com
 */

session_start();
// session_destroy();
// Prevent direct access to this class

define("BASEPATH", 1);

use PitchMaven\Api\Utility;
use PitchMaven\Data\DataOperations;
use PitchMaven\Api\Dal\AuthDal;

/**
 * PitchMaven Items Data Access Layer Class.
 * Data Access Layer DAL
 * 
 * PHP Version 8.1.3
 * 
 * @category Web_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */
class PaymentDal extends DataOperations
{
    private static $_input_data;
    private $_host_url = null;
    private static $_utility = null;
    private const BAD_REQUEST = "HTTP/1.0 400 Bad Request";
    private static $_payment = null;
    private static $_prefix = "pmp_"; // Change this to the name of your business or app
    private static $_overrideRef = false;
    private static $_user_details = [];

    /**
     * Class constructor
     * 
     * @param array $data  An array of user input data.
     * @param array $files file to be uploaded.
     * 
     * @return array
     */
    public function __construct(array $data = null)
    {        
        if (!is_null($data)) {
            unset($data['action']);
            self::$_input_data = $data;
        }

        $this->_host_url = $_SERVER['HTTP_HOST'];
        self::$_utility = new Utility();                
    }

    /**
     * Get payment url method.
     * Gets the url for a given payment.
     * 
     * @param string $url  the payment url.
     * @param array  $data require payment fields
     * 
     * @return string
     */
    public static function getURL($url, $data = array())
    {
        $urlArr = explode('?', $url);
        $params = array_merge($_GET, $data);
        $new_query_string = http_build_query($params) . '&' . $urlArr[1];
        $newUrl = $urlArr[0] . '?' . $new_query_string;
        return $newUrl;
    }

    /**
     * Generate payment reference method.
     * Generates a reference for donations.
     * 
     * @return array
     */
    public static function generateRef()
    {  
        static::$table = "pm_payments_tbl";
        static::$pk = "id";
        $count = self::countAll();
        $lastId = self::lastSavedId();
        if ($count <= 0) {
            $count = 1;
        } else {
            $count = $count + 1;
        }
        $payRef = uniqid(static::$_prefix).$count;
        return [
            'statuscode' => 0, 
            'status' => 'token created '. $lastId, 
            'data' => $payRef
        ];
    } 

    
    /**
     * Process payment for donations method.
     * Processes the donation payment on request
     * 
     * @return array
     */
    public function processPayments()
    {
        static::$table = "pm_payments_tbl";
        static::$pk = "id";
        $response = array();
        $ref = self::generateRef();

        // $rof = $postData['amount'] - (10.05 * 100);

        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            exit(header(self::BAD_REQUEST));          
        }
        
        $jwt = $matches[1];
        if (empty($matches) || empty($jwt)) {
            exit(header(self::BAD_REQUEST));
        }
        $item = explode('_', base64_decode($jwt));
        $verify_jwt = self::$_utility->decodeJWTToken($item[3], $item[0]);
        
        if ($verify_jwt->valid) {

            $post_url = "https://api.flutterwave.com/v3/payments";

            $name = self::$_input_data["last_name"]." ".self::$_input_data["first_name"];

            $post_data = array(
                "tx_ref"=>$ref["data"],
                "currency"=> self::$_input_data["currency"],
                "amount"=>self::$_input_data["amount"],
                "customer"=>array(
                    "name"=>$name,
                    "email"=>self::$_input_data["email"],
                    "phone_number"=>self::$_input_data["mobile"]
                ),
                "customizations"=>array(
                    "title"=>self::$_input_data["payment_title"],
                    "description"=>"Payment for tournament subscription",
                    "logo"=>"https://pitchmavenapi.bootqlass.com/assets/img/PitchMaven_web_logo.png"
                ),

                "meta"=>array(
                    "first_name"=>self::$_input_data["first_name"],
                    "last_name"=>self::$_input_data["last_name"],
                    "reason"=> "For the love of the game",
                    "comment"=> self::$_input_data["comment"]
                ),
                "redirect_url"=>"https://pitchmavenapi.bootqlass.com/?action=verify_payment"
            );

            $result = (object) self::handleCURL($post_data, $post_url);
            
            $saveable_data = [
                "first_name"=>self::$_input_data["first_name"],
                "last_name"=>self::$_input_data["last_name"],
                "email"=>self::$_input_data["email"],
                "mobile"=>self::$_input_data["mobile"],
                "country"=>self::$_input_data["country"],
                "currency"=>self::$_input_data["currency"],
                "amount"=>self::$_input_data["amount"],
                "tx_ref"=>$ref["data"]
            ];
            self::$_user_details = self::$_input_data;
            self::save($saveable_data);
            
            //Save user data to user table on successful payment verification
            $save_user = new AuthDal(self::$_input_data);
            $save_user->signUp();
            
            $response = $result->data->link;        
        } else {
            exit(header("HTTP/1.1 500 Internal Server Error <br> You are not allowed to access this page"));
        }
        return $response;
    }

    /**
     * Process payment response
     * Process the payment response from initiated payment
     * 
     * @return array
     */
    public function verifyPayments()
    {
        static::$table = "pm_payments_tbl";
        static::$pk = "tx_ref";
        $response = array();
        // if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        //     exit(header(self::BAD_REQUEST));          
        // }
        
        // $jwt = $matches[1];
        // if (empty($matches) || empty($jwt)) {
        //     exit(header(self::BAD_REQUEST));
        // }
        // $item = explode('_', base64_decode($jwt));
        // $verify_jwt = self::$_utility->decodeJWTToken($item[3], $item[0]);
        // if ($verify_jwt->valid) {
                
            $data = self::$_input_data;
            if ($data['status'] === "successful") {
                $url = "https://api.flutterwave.com/v3/transactions/{$data["transaction_id"]}/verify";

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 2);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer {$_SERVER["SECRET_KEY"]}",
                    "Content-Type: application/json"
                ]);

                $result = curl_exec($curl);

                $error = curl_error($curl);
                if ($error) {
                    $response = json_decode($error);
                }
                curl_close($curl);
                $response_data = json_decode($result);
                
                if ($response_data->status == "success" && $response_data->data->status == "successful" AND $response_data->data->id = $data["transaction_id"]) {                    
                    $saveable_data = [
                        "transaction_id"=>$response_data->data->id,
                        "tx_ref"=>$response_data->data->tx_ref,
                        "flw_ref"=>$response_data->data->flw_ref,
                        "device_fingerprint"=>$response_data->data->device_fingerprint,
                        "charged_amount"=>$response_data->data->charged_amount,
                        "app_fee"=>$response_data->data->app_fee,
                        "merchant_fee"=>$response_data->data->merchant_fee,
                        "processor_response"=>$response_data->data->processor_response,
                        "auth_model"=>$response_data->data->auth_model,
                        "ip"=>$response_data->data->ip,
                        "narration"=>$response_data->data->narration,
                        "status"=>$response_data->data->status,
                        "payment_type"=>$response_data->data->payment_type,
                        "account_id"=>$response_data->data->account_id,
                        "amount_settled"=>$response_data->data->amount_settled,
                        "first_6digits"=>$response_data->data->card->first_6digits,
                        "last_4digits"=>$response_data->data->card->last_4digits,
                        "issuer"=>$response_data->data->card->issuer,
                        "type"=>$response_data->data->card->type,
                        "token"=>$response_data->data->card->token,
                        "expiry"=>$response_data->data->card->expiry,
                        "transaction_country"=>$response_data->data->card->country,
                        "customer_id"=>$response_data->data->customer->id,
                        "payment_date"=>$response_data->data->created_at
                    ];
                    $check = static::findOne(['tx_ref'=> $response_data->data->tx_ref]);
                    if ($check) {
                        // die(var_dump(self::$_input_data, self::$_user_details));
                        //Save user data to user table on successful payment verification
                        // $save_user = new AuthDal(self::$_user_details);
                        // $save_user->signUp();

                        //check if user data saved into payment table successfully
                        if (self::update($saveable_data)) {
                            // $response = ["statuscode" => 0, "status" => "Thank you for your donation"];
                            header("Location:https://pitchmaven.bootqlass.com/pages/signup_success.html");
                            exit();
                        } else {
                            $response = ["statuscode" =>-1, "status" => "Unable to complete your donation at the moment"];
                        }
                    } else {                        
                        if (self::save($saveable_data)) {
                            // $response = ["statuscode" => 0, "status" => "Thank you for your donation"];
                            header("Location:https://pitchmaven.bootqlass.com/pages/signup_success.html");
                            exit();
                        } else {
                            $response = ["statuscode" =>-1, "status" => "Unable to complete your donation at the moment"];
                        }
                    }
                } else {
                    $response = ["statuscode" =>-1, "status" => "error", "data" =>$response_data];
                }
            } else {
                $response = ["statuscode" =>-1, "status" => "error", "data" =>$data];
            }
        // } else {
        //     exit(header("HTTP/1.1 500 Internal Server Error <br> You are not allowed to access this page"));
        // }
        return $response;
    }


    /**
     * Handle CURL Operation Method.
     * Handles CURL Operation and returns response based on given parameters
     * 
     * @param array $post_data
     * @param string $post_url
     * 
     * @return array
     */
    private static function handleCURL(Array $post_data, String $post_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        
        curl_setopt($curl, CURLOPT_URL, $post_url);
        
        curl_setopt($curl, CURLOPT_POST, 1);
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 200);

        curl_setopt($curl, CURLOPT_TIMEOUT, 200);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ". $_SERVER["SECRET_KEY"],
            "Content-Type: application/json",
            "Cache-Control: no-cache"
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }    
}
