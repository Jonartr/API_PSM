<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
     require_once 'Index.php';
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT * FROM usuarios WHERE email = '$email' AND pass_word = $password;";
     $resultado = $conn->query($query);
    if($conn->affected_rows > 0){
                while($row=$resultado->fetch_assoc()){
                    $array = $row;
                }
                header('Content-Type: application/json');
                echo json_encode($array);
    }
    else{
        echo "No se encontraron registros";
    }
    $resultado->close();
    $conn->close();
}

?>