<?php
include("conexion.php");

// Incluir PHPMailer manual
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$dato = $_POST['dato'];

// Buscar usuario por nombre o mail
$sql = "SELECT u.id_usuario, e.mail 
        FROM usuarios u 
        INNER JOIN empleados e ON e.id_empleado = u.id_empleado 
        WHERE u.usuario = ? OR e.mail = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $dato, $dato);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo  "<script>
            alert('❌ No se encontró ningún usuario con ese dato.');
            window.history.back();
          </script>";
    exit;
}

$user = $result->fetch_assoc();
$id_usuario = $user['id_usuario'];
$email = $user['mail'];

// Crear token y guardar en tabla
$token = bin2hex(random_bytes(16));
$expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));
$conn->query("INSERT INTO password_resets (id_usuario, token, expiracion) VALUES ($id_usuario, '$token', '$expiracion')");

// Enviar Gmail
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'priscillaguerrero52@gmail.com';
    $mail->Password = 'nska qxuh nqhg gupc';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
     $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    $mail->setFrom('priscillaguerrero52@gmail.com', 'Gestion');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Subject = 'Recuperar Contraseña';

    $link = "http://localhost/logistica_ezev2/restablecer.php?token=$token";
    $mail->Body =" <h3>Hola</h3>
        <p>Has solicitado recuperar tu contraseña. Haz clic en el siguiente enlace:</p>
        <p><a href='$link'>$link</a></p>
        <p>El enlace expirará en 1 hora.</p>"
   ;
    $mail->send();
    echo "<script>
            alert('📩 Correo enviado correctamente a $email. Revisa tu bandeja.');
            window.location.href = 'LoginCamiones.php';
          </script>";
} catch (Exception $e) {
    echo  "<script>
            alert('⚠️ Error al enviar el correo: {$mail->ErrorInfo}');
            window.history.back();
          </script>";
}
?>