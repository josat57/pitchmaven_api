<?php

namespace PitchMaven\Api;

/**
 * Utility class that holds utility functionalities
 * PHP Version 8.1.6
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <josephsamuelw1@gmail.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.coms.com
 */

use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\Exceptions;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;

require_once __DIR__ ."/JWTConfig.php";

/**
 * Utility class that holds utility functionalities
 * PHP Version 8.1.3
 *
 * @category MicroService_API_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <josephsamuelw1@gmail.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.coms.com
 */
class Utility
{
    public $mail;
    private static $_JWTClass;
    /**
     * The constructor
     */
    public function __construct()
    {
        self::$_JWTClass = new JWT();
    }

     /**
     * Email notification method
     * Sends email notification to a given email address
     * 
     * @param object $data an object of parameters
     * 
     * @return mixed
     */
    public function sendEmailNotification($data)
    {
        $mail = new PHPMailer(true);
        $response = "";
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->SMTPDebug = 0;                      //Enable verbose debug output
            $mail->isSMTP();   
            $mail->Mailer = 'smtp';
            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true
                )
            );                                         //Send using SMTP
            $mail->Host = MAIL_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = MAIL_USER;                     //SMTP username
            $mail->Password = MAIL_PASSWORD;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTP;            //Enable implicit TLS encryption
            $mail->SMTPSecure = "ssl";            //Enable implicit TLS encryption
            $mail->Port = MAIL_PORT;               //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`           

            //Recipients
            $mail->setFrom($data->sender, $data->appName);
            $mail->addAddress($data->email, $data->name);     //Add a recipient
            $mail->addAddress($data->email);               //Name is optional
            $mail->addReplyTo(MAIL_REPLY_TO, 'Notification');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $data->subject;
            $mail->Body    = $data->message;
            $mail->AltBody = '';

            $response =$mail->send();
            
        } catch (Exception $exc) {
            $response = $exc;
        }
        return json_encode($response);
    }

    /**
     * Check directory for upload
     * 
     * @param string $dir the path to the directory.
     * 
     * @return string
     */
    private static function _dirt($dir)
    {
        $new_dir = str_replace(' ', '', $dir);
        $applpicsdir = 'file_server';

        if (!is_dir($applpicsdir)) {
            $applpicsdir = "file_server";
        } else {
            $applpicsdir = (is_link($applpicsdir)?readlink($applpicsdir):$applpicsdir)."/{$_SERVER['HTTP_HOST']}/{$new_dir}";
        }

        $user_dir = self::_getRelativePath("$applpicsdir");

        return $user_dir = (is_link($user_dir)?readlink($user_dir):$user_dir);
    }

   /**
     * Upload team and gift item's image
     * 
     * @param array  $file Object file details
     * @param string $dir  directory name to upload image
     * @param object $data Object of data parsed
     * 
     * @return array
     */
    public static function uploadItems($file, $dir, $data):array
    {
        $response = null;
        $uploadOk = null;
        $imageFileType = explode("/", $file['item_image']["type"]);
        $filename = str_replace(' ', '', strtolower($data['item_code'])).".".$imageFileType[1];
        
        if (empty($file['item_image']["tmp_name"]) || $file['item_image']["error"] > 0 ) {
            $uploadOk = ['status' => 'Please Select an item image to upload.', 'statuscode' => -1];
        } else {
            $uploadOk = 1;
        }

        if ($imageFileType[0] === "image") {
            $uploadOk = 1;
        } else {
            $uploadOk = ['status' => "The item image must be an image", 'statuscode' => -2];
        }
        
        if ($file['item_image']["size"] <= 2000000) {
            $uploadOk = 1;
        } else {
            $uploadOk = ['status' => 'The item image you is too large', 'statuscode' => -3];
        }
        
        $formats = ['JPEG', 'JPG', 'jpg', 'jpeg', 'png', 'PNG'];
        
        if (!in_array($imageFileType[1], $formats)) {
            $uploadOk = ['status' => "The item image must be a PNG or JPEG image", 'statuscode' => -4];
        }

        if ($uploadOk != 1) {
            $response = ['status' => $uploadOk['status'], 'statuscode' => $uploadOk['statuscode']];
        } else {
            $response = (array) self::uploadToServer($file, $dir, $filename, "items"); 
        }
        return $response;        
    }

    /**
     * Upload team and palyer images
     * 
     * @param object $data Object of data parsed
     * @param array  $file Object file details
     * @param string $dir  directory name to upload image
     * 
     * @return Array
     */
    public static function uploadImages($file, $dir)
    {                       
        $dt = strtotime('now');
        $target_dir = strtolower(str_replace(' ', '', $dir));
        $uploadOk = 1;

        $imageFileType = explode("/", $file['profile_photo']["type"]);

        $name = uniqid("gkpf")."_".$dt;

        $fileName = str_replace(' ', '', $name).".".$imageFileType[1];

        $target_file = strtolower($target_dir);

        $check = explode("/", $file['profile_photo']["type"]);
               
        if (empty($file['profile_photo']["tmp_name"]) || $file['profile_photo']["error"] > 0 ) {
            $uploadOk = ['status' => 'Please Select a profile image to upload.', 'statuscode' => -1];
        } else {
            $uploadOk = 1;
        }
        
        if ($check[0] === "image") {
            $uploadOk = 1;
        } else {
            $uploadOk = ['status' => "The image must be an image", 'statuscode' => -2];
        }
        
        if ($file['profile_photo']["size"] <= 2000000) {
            $uploadOk = 1;
        } else {
            $uploadOk = ['status' => 'The image you is too large', 'statuscode' => -3];
        }
        
        $formats = ['JPEG', 'JPG', 'jpg', 'jpeg', 'png', 'PNG'];
        
        if (!in_array($imageFileType[1], $formats)) {
            $uploadOk = ['status' => "The item image must be a PNG or JPEG image", 'statuscode' => -4];
        }

        if (strtolower($imageFileType[1]) != "jpg" && strtolower($imageFileType[1]) != "png" && strtolower($imageFileType[1] != "jpeg")) {
            $uploadOk = 3;
        }

        if ($uploadOk != 1) {
            $response = ['status' => true, 'statuscode' => $uploadOk];
        } else {        
            $response = self::uploadToServer($file, $target_file, $fileName, "profile");
            
            // if (array_key_exists("statuscode", $result) && $result["statuscode"] == -1) {
            //     $response = $result;   
            // } elseif (array_key_exists("statuscode", $result) && $result["statuscode"] == 0) {
            //     $response = [
            //         'status' => $result['status'], 
            //         'statuscode' => $result['statuscode'], 
            //         'filename' =>$result['filename'], 
            //         'target_dir' => $dir
            //     ];
            // } else {
            //     $response = $result;  
            // }
        }
        return $response;        
    }
    

    /**
     * Upload to server method.
     * Uploads file to server.
     * 
     * @param object $file          the file to upload.
     * @param array  $data          data containing file details.
     * @param string $dir           the directory to place the file.
     * @param array  $imageFileType the type of file.
     * 
     * @return array
     */
    public static function uploadToServer($file, string $dir, string $file_name, String $action)
    {      
        $type = null; 
        if ($action == "items") {
            $type = "item_image";
        } else if ($action == "profile") {
            $type = "profile_photo";
        }
        $fileName = $file[$type]["name"];
        $fileTmp = $file[$type]["tmp_name"];
        $fileSize = $file[$type]["size"];
        $fileType = $file[$type]["type"];

        // Open the image file for reading
        $_file = fopen($fileTmp, "r");
        $_files = curl_file_create(realpath($fileTmp), $fileType, $fileName);
        $remoteData = array(
            // 'file' => $_file,
            // 'name' => $fileName,
            // 'type' => $fileType,
            // 'size' => $fileSize,
            'file' => $_files,
            'dir' => $dir,
            'file_name'=> $file_name,
            'action' => $action,
        );
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://pitchmaven.bootqlass.com/server/");
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $remoteData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $response = (array) json_decode(curl_exec($curl));
        $error = curl_error($curl);
        curl_close($curl); 
        fclose($_file);
        if ($error) {
            $response = ['statuscode' -1, 'status' => $error];
        }
        return $response;
    } 

    /**
     * Get uploaded images
     * 
     * @param string $dir     the file directory
     * @param string $theFile the name of the file
     * 
     * @return string
     */
    public static function getUploadedImagesFromServer($dir, $theFile) 
    {                     
        $url = "https://pitchmaven.bootqlass.com/server/file_server/$dir/$theFile";
        
        if (empty(@file_get_contents($url)) || @file_get_contents($url) === false) {
            $response = ['statuscode' => 404, 'photo' => "N/A "];
        } else {
            $imageFile = @file_get_contents($url);
            $response = ['statuscode' => 200, 'photo' => base64_encode($imageFile)];
        }        
        return $response;
    }

    /**
     * Get uploaded images
     * 
     * @param string $dir     image directory
     * @param string $theFile image
     * 
     * @return string
     */
    public static function getUploadedImages($dir, $theFile) 
    {      
        // $applpicsdir = self::_dirt($dir);
        // $dirt = $applpicsdir;
        // $retval = [];
        // if (substr($dirt, -1) != "/") {
        //     $dirt .= "/";
        // } 
        $dirt = "https://pitchmaven.bootqlass.com/server/file_server/$dir";
        
        // if (is_dir($dirt.=$dir)) {                            
        if (is_dir($dirt)) {                            
            if ($handle = opendir($dirt)) {
                while (false !== ($file = readdir($handle))) {                
                    if ($file !== "." && $file !== "..") {
                        $imgfile = file_get_contents($dirt."/".$theFile);
                        $retval['photo'] = base64_encode($imgfile);         
                    } else {
                        $retval['photo'] = null;
                    }
                }            
                closedir($handle);
            } else {
                $retval['photo'] = null;
            }
        } else {
            $retval['photo'] = null;
        }
        return $retval;
    }

    /**
     * Delete uploaded images
     *
     * @param string $dir     image directory
     * @param string $thefile image
     *
     * @return string
     */
    public static function deleteUploadedImages($dir, $thefile) 
    {
        $applpicsdir = self::_dirt($dir);
        $dirt = $applpicsdir;
        $retval = "";
        if (substr($dirt, -1) != "/") {
            $dirt .= "/";
        }
        if (is_dir($dirt.=$dir) && $handle = opendir($dirt)) {
            while (false !== ($file = readdir($handle))) {
                if ($file !== "." && $file !== "..") {
                    $imgfile = $dirt."/".$thefile;
                    if (file_exists($imgfile)) {
                        $retval = unlink($imgfile);
                    } else {
                        $retval = 10;
                    }
                }
            }
            closedir($handle);
        }
        return $retval;
    }

    /**
     * Get relative path of address
     *
     * @param string $path a string of directory path or url
     *
     * @return string
     */
    private static function _getRelativePath($path)
    {
        $path = preg_replace("#/+\.?/+#", "/", str_replace("\\", "/", $path));
        $dirs = explode("/", rtrim(preg_replace('#^(\./)+#', '', $path), '/'));

        $offset = 0;
        $sub = 0;
        $subOffset = 0;
        $root = "";

        if (empty($dirs[0])) {
            $root = "/";
            $dirs = array_splice($dirs, 1);
        } else if (preg_match("#[A-Za-z]:#", $dirs[0])) {
            $root = strtoupper($dirs[0]) . "/";
            $dirs = array_splice($dirs, 1);
        }

        $newDirs = array();
        foreach ($dirs as $dir) {
            if ($dir !== "..") {
                $subOffset--;
                $newDirs[++$offset] = $dir;
            } else {
                $subOffset++;
                if (--$offset < 0) {
                    $offset = 0;
                    if ($subOffset > $sub) {
                        $sub++;
                    }
                }
            }
        }

        if (empty($root)) {
            $root = str_repeat("../", $sub);
        }
        return $root . implode("/", array_slice($newDirs, 0, $offset));
    }

    /**
     * Finds the schema method.
     * 
     * Finds and returns the schema an object's data is stored in.
     * 
     * @param string $fields   a string of fields separated with ',' 
     *                         to be returned when found
     * @param string $params   a string of parameters parsed based on set criteria
     * @param string $criteria a string of criteria to be used in the query
     * @param object $db       a database connection Object
     * 
     * @return string
     */
    public static function find(String $fields, String $params, String $criteria, Object $db)
    {
        $schema = ['admins', 'members'];
        $table = null;
        foreach ($schema as $field) {
            $result = $db->qry("SELECT {$fields} FROM {$field} WHERE {$criteria} = '?'", $params);
            if ($result->num_rows > 0) {
                $table = $field;
                break;
            }
        }        
        return $table;
    }

    
    /**
     * Generate jwt token
     * Generates a jwt token based on given parameters
     *
     * @param array $data the data to generate token
     *
     * @return object
     */
    public function generateJWTToken(array $data = null)
    {
        $request_data = JWT_DATA;
        if (!is_null($data)) {
            $request_data['userName'] = $data['user_id'];
            $request_data['maggie'] = base64_encode($data['user_id']);
        }
        $jwt = new TokenDecoded($request_data);
        $tokenEncoded = $jwt->encode(PRIVATE_KEY, JWT::ALGORITHM_RS256);
        return $tokenEncoded->toString();
    }

    /**
     * Decode jwt token
     * Decodes a jwt token based on received jwt token from client's request
     *
     * @param array $token   the data to generate token.
     * @param array $user_id the data to generate token.
     *
     * @return object
     */
    public function decodeJWTToken(string $token = null, string $user_id = null)
    {
        $jwt = json_encode($token);
        $tokenEncoded = new TokenEncoded($jwt);
        
        $serverName = rawurldecode(parse_url($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], PHP_URL_PATH));
        // $leeway = 500;
        // $tokenEncoded->validate(PUBLIC_KEY, JWT::ALGORITHM_RS256, $leeway);
        try {
       
            $token = (object) $tokenEncoded->decode()->getPayload();
            
            if ($token->iss === $serverName
                && $token->nbf === $token->iat
                && $token->exp > $token->iat
                && $token->userName == $user_id
            ) {
                $token->valid = true;
                $response = $token;
            } else {
                $token->valid = false;
                $response = $token;
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }
}