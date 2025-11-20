<?php
require_once(__DIR__ . '/../models/PostModel.php');

class PostController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Publicaciones();
    }

    public function cargaImagenes($data, $photoid)
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            if (isset($_FILES['imagenes'])) {

                $archivosCargados = [];
                $archivosServidor = [];


                if (is_array($_FILES['imagenes']['name'])) {
                    for ($i = 0; $i < count($_FILES['imagenes']['name']); $i++) {
                        if (
                            $_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK &&
                            !empty($_FILES['imagenes']['name'][$i])
                        ) {
                            $archivosCargados[] = $_FILES['imagenes']['name'][$i];


                            $archivoIndividual = [
                                'name' => $_FILES['imagenes']['name'][$i],
                                'type' => $_FILES['imagenes']['type'][$i],
                                'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                                'error' => $_FILES['imagenes']['error'][$i],
                                'size' => $_FILES['imagenes']['size'][$i]
                            ];

                            $email = $data['email'];
                            $ruta = $this->saveImagePost($archivoIndividual, $email);
                            $archivosServidor[] = "https://apipsm-production.up.railway.app/$ruta";
                            $photodata = [
                                "image" => $ruta ? "https://apipsm-production.up.railway.app/$ruta" : null,
                                "email" => $email,
                                "idphoto" => $photoid
                            ];

                            $result_2 =  $this->postModel->loadImage($photodata);

                            if ($result_2) {
                                $this->sendResponse(201, ["message" => "Publicacion creada"]);
                            } else {
                                $this->sendResponse(404, ["message" => "Error al crear publicacion"]);
                            }
                        }
                    }
                } else if (
                    $_FILES['imagenes']['error'] === UPLOAD_ERR_OK &&
                    !empty($_FILES['imagenes']['name'])
                ) {
                    $archivosCargados[] = $_FILES['imagenes']['name'];
                    $ruta = $this->saveImagePost($_FILES['imagenes'], "Jona");
                    $archivosServidor[] = "https://apipsm-production.up.railway.app/$ruta";
                }
            } else {
                $this->sendResponse(400, ["message" => "Error al cargar imagenes"]);
            }
        }
    }

    public function create()
    {

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'multipart/form-data') !== false) {

            $title = $_POST['title'];
            $description = $_POST['description'];
            $email = $_POST['email'];


            if (!isset($title) || !isset($description) || !isset($email)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $data = [
                'title' => $title,
                'description' => $description,
                'email' => $email

            ];

            $result = $this->postModel->nuevoPost($data);

            if ($result != null) {

                if (isset($_FILES['imagenes'])) {
                    $this->cargaImagenes($data, $result);
                } else {
                    $this->sendResponse(404, ["message" => "No se encontro imagen"]);
                }

                $this->sendResponse(201, ["message" => "Publicacion Correcta"]);
            } else {
                $this->sendResponse(401, ["message" => "Error al crear publicacion"]);
            }
        }
    }

    public function update()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'multipart/form-data') !== false) {

            $title = $_POST['title'];
            $description = $_POST['description'];
            $email = $_POST['email'];
            $id = $_POST['idstory'];


            if (!isset($title) || !isset($description)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $data = [
                'title' => $title,
                'description' => $description,
                'email' => $email,
                'idstory' => $id

            ];

            $result = $this->postModel->actualizarPost($data);

            if ($result != null) {

                $this->postModel->deleteImage($id);
                
                if (isset($_FILES['imagenes'])) {
                    $this->cargaImagenes($data, $id);

                    $this->sendResponse(201, ["message" => "Actualizacion de publicacion correcta"]);
                } else {
                    $this->sendResponse(404, ["message" => "No se encontro imagen"]);
                }
            } else {
                $this->sendResponse(401, ["message" => "Error al actualizar publicacion"]);
            }
        }
    }

    public function delete()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode(file_get_contents("php://input"), true);

            $id = $data['idPost'];

            $result =  $this->postModel->eliminarPost($id);

            if ($result) {
                $this->sendResponse(200, ["message" => "Publicacion eliminada"]);
            }
        } else {
            $this->sendResponse(401, ["message" => "Error en formato"]);
        }
    }

    private function saveImagePost($image, $email)
    {
        $uploadPath = "data/post/" . $email . "/" . "images/";


        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $email . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadPath . $fileName;

        if (move_uploaded_file($image['tmp_name'], $filePath)) {
            error_log("Imagen guardada en: $filePath");
            return $filePath;
        } else {
            error_log("Error moviendo archivo: " . $image['tmp_name'] . " a " . $filePath);
            $message = "Error moviendo archivo: " . $image['tmp_name'] . " a " . $filePath;
            return $message;
        }
    }

    public function getpost()
    {
        $result = $this->postModel->loadPost();
        $this->sendResponse(200, $result);
    }



    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}


/*  $imagenPath = $this->saveImagePost($_FILES['imagen'], $email);
                    if ($imagenPath != null) {
                        $photodata = [
                            "image" => $imagenPath ? "https://apipsm-production.up.railway.app/$imagenPath" : null,
                            "email" => $email,
                            "idphoto" => $result
                        ];

                        $result_2 =  $this->postModel->loadImage($photodata);

                        if ($result_2) {
                            $this->sendResponse(201, ["message" => "Publicacion creada"]);
                        } else {
                            $this->sendResponse(404, ["message" => "Error al crear publicacion"]);
                        }
                    } */