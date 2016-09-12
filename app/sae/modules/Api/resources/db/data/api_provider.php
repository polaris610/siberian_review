<?php
$api_providers = array(
    array(
        "code" => "instagram",
        "icon" => "fa-instagram",
        "keys" => array(
            "token",
            "client_id"
        )
    ),
    array(
        "code" => "facebook",
        "icon" => "fa-facebook-square",
        "keys" => array(
            "app_id",
            "secret_key"
        )
    ),
    array(
        "code" => "youtube",
        "icon" => "fa-youtube",
        "keys" => array(
            "api_key"
        )
    ),
    array(
        "code" => "soundcloud",
        "icon" => "fa-soundcloud",
        "keys" => array(
            "client_id",
            "secret_id"
        )
    )
);

foreach($api_providers as $api_provider) {

    $data = array(
        "code" => $api_provider["code"],
        "name" => ucfirst($api_provider["code"]),
        "icon" => $api_provider["icon"],
    );

    $provider = new Api_Model_Provider();
    $provider
        ->setData($data)
        ->insertOnce(array("code"));
    
    foreach($api_provider["keys"] as $key) {
        $data = array(
            'provider_id' => $provider->getId(),
            'key' => $key,
        );

        $key = new Api_Model_Key();
        $key
            ->setData($data)
            ->insertOnce(array("provider_id", "key"));

    }

}

# Delete instagram from backoffice
$provider = new Api_Model_Provider();
$provider->find("instagram", "code");
if($provider->getId()) {
    $provider->delete();
}
