<?php

require_once(__DIR__ . '/../models/CommentModel.php');

class CommentController
{

    private $Comment;

    public function __construct()
    {
        $this->Comment = new Comentario();
    }

    public function newcomment()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $data = json_decode(file_get_contents("php://input"), true);

            $id = $data['id'];
            $email = $data['email'];
            $comment = $data['comment'];

            if (!isset($id) || !isset($email) || !isset($comment)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $commentData = [
                'id' =>  $id,
                'email' =>  $email,
                'comment' => $comment
            ];

            $result = $this->Comment->insertarComentario($commentData);

            if ($result) {
                $this->sendResponse(201, ["message" => "Comentario agregado"]);
            } else {
                $this->sendResponse(404, ["message" => "Error comentario", "data" => $result]);
            }
        } else {
            $this->sendResponse(404, ["message" => "Error de JSON"]);
        }
    }

    public function deleteComments()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'];
            $email = $data['email'];

             $commentData = [
                'id' =>  $id,
                'email' =>  $email
            ];

               $result = $this->Comment->eliminarComentario($commentData);

               
            if ($result) {
                $this->sendResponse(201, ["message" => "Comentario agregado"]);
            } else {
                $this->sendResponse(404, ["message" => "Error al borrar comentario", "data" => $result]);
            }

        }
    }

    public function getComments()
    {
        $result = $this->Comment->loadComments();
        $this->sendResponse(200, $result);
    }


    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
