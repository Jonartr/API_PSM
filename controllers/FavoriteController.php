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
            }
        }
    }

    public function getFavorite()
    {
          if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $data = json_decode(file_get_contents("php://input"), true);
               $email = $data['email'];
                $result = $this->Favorite->loadFavorite( $email);
                $this->sendResponse(200, $result);
          }
          else{
               $this->sendResponse(404, ["message" => "Error de formato JSON"]);
          }
      
    }

    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
