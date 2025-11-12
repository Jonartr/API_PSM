<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
    require_once 'Index.php';
    echo "Si entra aqui";
    $email = $_POST["email"];
    $password = $_POST["password"];
    $name = $_POST["name"];
    $lastname = $_POST["lastname"];
    $alias = $_POST["user"];
    $query = "INSERT INTO usuarios (email, pass_word, nam_e, last_name, alias) VALUES ('$email', '$password','$name','$lastname','$alias')";
    $resultado = $conn->query($query);
    if($resultado == true){
        echo "Datos ingresados correctamente";
    }
    else{
        echo "Ha ocurrido un error";
    }
        $resultado->close();
    $conn->close();
}

?>