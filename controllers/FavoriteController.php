<?php

require_once(__DIR__ . '/../models/FavoriteModel.php');

class FavoriteController
{
    private $Favorite;

    public function __construct()
    {
        $this->Favorite = new Favorito();
    }

    public function newFavorite()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $data = json_decode(file_get_contents("php://input"), true);

            $idfav = $data['id'];
            $email = $data['email'];

            $favData = [
                'id' =>  $idfav,
                'email' =>  $email
            ];

            $result = $this->Favorite->addFavorite($favData);

            if (!isset($idfav) || !isset($email)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            } else {
                $this->sendResponse(201, ["message" => $result]);
            }
        }
    }

    public function eliminarFavorito()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
             $data = json_decode(file_get_contents("php://input"), true);

            $idfav = $data['id'];
            $email = $data['email'];

            $favData = [
                'id' =>  $idfav,
                'email' =>  $email
            ];

            $result = $this->Favorite->deleteFavorite($favData);

            if($result){
                 $this->sendResponse(201, ["message" => "Favorito eliminado"]);
            }
            else{
                 $this->sendResponse(401, ["message" => "Error al eliminar favorito"]);
            }
        }
    }

    public function getFavorite()
    {
        $result = $this->Favorite->loadFavorite();
        $this->sendResponse(200, $result);
    }

    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
