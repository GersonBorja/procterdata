<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use Pusher\PushNotifications\PushNotifications;

class Notificacion {

    public function crearNotificacion($titulo, $cuerpo, $imagen='https://procter.work/public/img/logo/p&g.svg') {

        $beamsClient = new PushNotifications(array(
            "instanceId" => "60d87988-4708-453e-aab5-0b55703a8b2f",
            "secretKey" => "B0D22953E9CB7B6C4693BC73425F1E64C4740688809486214110BFC20C591503",
        ));

        $data = array(
            "title" => $titulo,
            "body" => $cuerpo,
            "icon" => $imagen,
            "deep_link" => "https://procter.work"
        );

        $publishResponse = $beamsClient->publishToInterests(
            array("noticias"),
            array(
                "web" => array(
                    "notification" => $data
                ),
            )
        );

        $mensaje = [
            "status" => "ok",
            "mensaje" => "Notificacion enviada"
        ];

        return $mensaje;

    }

}
