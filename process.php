<?php
// Configure your Subject Prefix and Recipient here

//$subjectPrefix = '[Contact via website]';
$emailTo       = 'moodle@unila.edu.mx';
$errors = array(); // array to hold validation errors
$data   = array(); // array to pass back data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['name']));
    $email   = stripslashes(trim($_POST['email']));
    $subject = stripslashes(trim($_POST['subject']));
    $message = stripslashes(trim($_POST['message']));
    $seccion = stripslashes(trim($_POST['seccion']));
    $telefono = stripslashes(trim($_POST['telefono']));
    if (empty($name)) {
        $errors['name'] = 'El nombre es requerido.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El correo electrónico es inválido';
    }
    if (empty($subject)) {
        $errors['subject'] = 'El asunto es requerido';
    }
    if (empty($message)) {
        $errors['message'] = 'El mensaje es requerido';
    }
    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {        
        
        if (ereg("[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]$", $name) && ereg("[a-zA-ZñÑ@\s]$", $email) && ereg("[a-zA-ZñÑáéíóúÁÉÍÓÚ.,\s]$", $subject) && ereg("[a-zA-ZñÑáéíóúÁÉÍÓÚ.,;\s]$", $message) && ereg("[0-9\s]$", $telefono)) {
            persistir_database($name, $email, $subject, $message, $seccion, $telefono);
            $data['success'] = true;               
        }
        else{
            $data['success'] = false;
        }        
    }
    // return all our data to an AJAX call
    echo json_encode($data);
}


function persistir_database($name, $email, $subject, $message, $seccion, $telefono){
    /*
    Conexion BD
    */
    $usuario = "portal";
    $password = "PedroPiedra";
    $servidor = "localhost";
    $database = "contacto_portal";


    date_default_timezone_set('America/Mexico_City');
    $date_time = date("Y-m-d H:i:s");
    $table = "contacto";

    // Create connection
    $conn = new mysqli($servidor, $usuario, $password, $database);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO $table (id, nombre, email, asunto, mensaje, seccion, fecha_hora, telefono) VALUES (NULL, '$name', '$email', '$subject', '$message', '$seccion', '$date_time', '$telefono')";

    if ($conn->query($sql) === FALSE){
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}