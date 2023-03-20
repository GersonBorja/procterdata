<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use Pusher\PushNotifications\PushNotifications;

class Notificacion {

    public function crearNotificacion($titulo, $cuerpo, $imagen='https://procter.work/public/img/logo/p&g.svg') {

        $beamsClient = new PushNotifications(array(
            "instanceId" => "",
            "secretKey" => "",
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