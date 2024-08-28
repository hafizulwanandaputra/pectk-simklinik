<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AuthModel;
use \Firebase\JWT\JWT;

class Login_api extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $model = new AuthModel();
        $input = $this->request->getRawInput();
        $username = $input['username'];
        $password = $input['password'];

        $user = $model->where('username', $username)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'Gagal masuk!'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Gagal masuk!'], 401);
        }

        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;

        $payload = array(
            "iss" => "Issuer of the JWT",
            "aud" => "Audience that the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "username" => $user['username'],
            "username" => $user['role'],
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'message' => 'Berhasil masuk sebagai @' . $user['username'] . ' (' . $user['role'] . ')! Silakan salin token di bawah ini.',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }
}
