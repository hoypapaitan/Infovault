<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use App\Models\MiscModel;
use App\Models\UsersModel;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;

class Auth extends BaseController
{
    protected $authModel;
    protected $miscModel;
    protected $userModel;

    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
        $this->userModel = new UsersModel();
    }

    public function privateKey(){
        $privateKey = <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
        vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
        5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
        AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
        bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
        Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
        cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
        5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
        ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
        k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
        qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
        eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
        B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
        -----END RSA PRIVATE KEY-----
        EOD;

        return $privateKey;
    }

    public function login(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $hasPass = sha1($data->password);

        // Find user by username
        $user = $this->authModel->where(['username' => $data->username])->get()->getRow();

        // If user exists
        if($user){
            // Check if account is locked
            if(isset($user->loginAttemps) && $user->loginAttemps >= 5){
                $response = [
                    'error' => 403,
                    'title' => 'Account Locked',
                    'message' => 'Your account is locked due to too many failed login attempts. Please contact an admin to unlock.'
                ];
                return $this->response
                        ->setStatusCode(403)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }

            // Check password
            if($user->password === $hasPass){
                // If account is active
                if($user->status == 1){
                    // Reset login attempts on successful login
                    $this->authModel->update($user->id, ['loginAttemps' => 0]);

                    $originalUserType = $user->userType;
                    $user->userType = $this->miscModel->getUserType($user->userType);

                    $secretKey = $this->privateKey();
                    $issueTimeClaim = time();
                    $notBeforeClaim = $issueTimeClaim + 10;
                    $expiryClaim = $issueTimeClaim + 3600;

                    $token = [
                        "fullName" => $user->firstName .' '. $user->lastName,
                        "iss" => $user->email,
                        "aud" => $originalUserType == 1 ? 'admin' : 'others',
                        "iat" => $issueTimeClaim,
                        "nbf" => $notBeforeClaim,
                        "exp" => $expiryClaim,
                        "userId" => $user->id,
                        "modules" =>  $user->userType->modules
                    ];

                    $jwt = JWT::encode($token, $secretKey, 'RS256');

                    $result = [
                        "fullName" => $user->firstName .' '. $user->lastName,
                        "userId" => $user->id,
                        "jwt" => $jwt,
                    ];

                        return $this->response
                            ->setStatusCode(200)
                            ->setJSON($result);
                } else {
                    $response = [
                        'title' => 'Account Deactivated',
                        'message' => 'Please contact your adminitrator for more information'
                    ];
                        return $this->response
                            ->setStatusCode(404)
                            ->setJSON($response);
                }
            } else {
                // Password incorrect, increment login attempts
                $attempts = isset($user->loginAttemps) ? $user->loginAttemps + 1 : 1;
                $this->authModel->update($user->id, ['loginAttemps' => $attempts]);

                // Lock account if attempts reach 5
                if($attempts >= 5){
                    $response = [
                        'error' => 403,
                        'title' => 'Account Locked',
                        'message' => 'Your account is locked due to too many failed login attempts. Please contact an admin to unlock.'
                    ];
                        return $this->response
                            ->setStatusCode(403)
                            ->setJSON($response);
                } else {
                    $response = [
                        'error' => 404,
                        'title' => 'Invalid Credentials',
                        'message' => 'Please check your username or password'
                    ];
                    return $this->response
                            ->setStatusCode(200)
                            ->setContentType('application/json')
                            ->setBody(json_encode($response));
                }
            }
        } else {
            $response = [
                'error' => 404,
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password'
            ];
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r(json_encode($data));
    }

    public function reconnect($uid){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $uid])->find();
        $user[0]['buildingInfo'] = $this->buildingModel->where(['premiseNumber' => $user[0]['premise']])->find();

        //Set Api Response return to the FE
        if($user){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($user));
        } else {
            $response = [
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password'
            ];

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON($response);
        }


        // print_r(json_encode($data));
        
    }   

    public function firstLoginChangePassword(){

        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->password, 'isFirstLogin' => 1];

        $updatePassword =  $this->authModel->updateBuildingInfo($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'Change Password',
                'message' => 'Your successfully change password.'
            ];
 
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON($response);
        } else {
            $response = [
                'title' => 'Change Password Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function forgotPassword(){
        // Get API Request Data from Frontend
        $data = $this->request->getJSON();
        
        // Validate username and email match
        $user = $this->userModel->where([
            'username' => $data->username, 
            'email' => $data->email
        ])->first();
        
        // Check if user exists with matching username and email
        if(!$user){
            $response = [
                'error' => 404,
                'title' => 'User Not Found',
                'message' => 'The email address may not exist or does not match the username provided.'
            ];
            
            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
        // Generate password reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
        
        // Store token in database (you may need to create a password_resets table)
        $db = \Config\Database::connect();
        $builder = $db->table('tblpassword_resets');
        
        // Delete any existing tokens for this email
        $builder->where('email', $data->email)->delete();
        
        // Insert new token
        $builder->insert([
            'email' => $data->email,
            'token' => $token,
            'expiry' => $expiry,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Send email with reset link
        $email = \Config\Services::email();
        
        // Ensure email config is loaded from environment
        $emailConfig = new \Config\Email();
        
        // Configure email settings (you may need to update these in app/Config/Email.php)
        $resetLink = "http://localhost:8083/reset-password/" . $token;
        
        $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
        $email->setTo($data->email);
        $email->setSubject('Password Reset Request - ASCOT InfoVault');
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #1890ff; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background-color: #f5f5f5; }
                .button { 
                    display: inline-block; 
                    padding: 12px 30px; 
                    background-color: #1890ff; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    margin: 20px 0;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Password Reset Request</h2>
                </div>
                <div class='content'>
                    <p>Hello " . $user['firstName'] . " " . $user['lastName'] . ",</p>
                    <p>We received a request to reset your password for your ASCOT InfoVault account.</p>
                    <p>Click the button below to reset your password:</p>
                    <p style='text-align: center;'>
                        <a href='" . $resetLink . "' class='button'>Reset Password</a>
                    </p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #1890ff;'>" . $resetLink . "</p>
                    <p><strong>This link will expire in 1 hour.</strong></p>
                    <p>If you didn't request a password reset, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2025 ASCOT InfoVault. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $email->setMessage($message);
        
        if($email->send()){
            $response = [
                'success' => true,
                'title' => 'Email Sent',
                'message' => 'A password reset link has been sent to your email address.'
            ];
            
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON($response);
        } else {
            $response = [
                'error' => 500,
                'title' => 'Email Failed',
                'message' => 'Failed to send reset email. Please try again later.',
                'debug' => $email->printDebugger(['headers'])
            ];
            
            return $this->response
                    ->setStatusCode(500)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function resetPassword(){
        // Get API Request Data from Frontend
        $data = $this->request->getJSON();
        
        // Validate token
        $db = \Config\Database::connect();
        $builder = $db->table('tblpassword_resets');
        
        $resetRecord = $builder->where('token', $data->token)
                              ->where('expiry >=', date('Y-m-d H:i:s'))
                              ->get()
                              ->getRow();
        
        if(!$resetRecord){
            $response = [
                'error' => 400,
                'title' => 'Invalid Token',
                'message' => 'The password reset link is invalid or has expired.'
            ];
            
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
        // Update user password
        $hashedPassword = sha1($data->newPassword);
        
        $updated = $this->userModel->where('email', $resetRecord->email)
                                   ->set(['password' => $hashedPassword])
                                   ->update();
        
        if($updated){
            // Delete used token
            $builder->where('token', $data->token)->delete();
            
            $response = [
                'success' => true,
                'title' => 'Password Reset Successful',
                'message' => 'Your password has been successfully reset. You can now login with your new password.'
            ];
            
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 500,
                'title' => 'Reset Failed',
                'message' => 'Failed to reset password. Please try again.'
            ];
            
            return $this->response
                    ->setStatusCode(500)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function validateResetToken(){
        // Validate if token is still valid
        $data = $this->request->getJSON();
        
        $db = \Config\Database::connect();
        $builder = $db->table('tblpassword_resets');
        
        $resetRecord = $builder->where('token', $data->token)
                              ->where('expiry >=', date('Y-m-d H:i:s'))
                              ->get()
                              ->getRow();
        
        if($resetRecord){
            $response = [
                'valid' => true,
                'email' => $resetRecord->email
            ];
            
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'valid' => false,
                'message' => 'Token is invalid or expired'
            ];
            
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

}