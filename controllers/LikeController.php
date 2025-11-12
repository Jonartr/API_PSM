<?php

require_once(__DIR__ . '/../models/LikeModel.php');

class LikeController
{
    private $Like;

    public function __construct()
    {
        $this->Like = new Like();
    }

    public function toggle()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $data = json_decode(file_get_contents("php://input"), true);

            $id = $data['id'];
            $email = $data['email'];
            $like = $data['like'];

            if (!isset($id) || !isset($email) || !isset($like)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $likeData = [
                'id' =>  $id,
                'email' =>  $email,
                'like' => $like
            ];

            $result = $this->Like->uplike($likeData);

            if ($result) {
                $this->sendResponse(201, ["message" => "Like agregado"]);
            } else {
                $this->sendResponse(404, ["message" => "Error comentario"]);
            }
        } else {
            $this->sendResponse(404, ["message" => "Error de JSON"]);
        }
    }

    public function checkLike()
    {
        $result = $this->Like->loadLike();
        $this->sendResponse(200, $result);
    }

    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
