<?php

class Player{
    /*
     * @Update: removed and added new 'id' property
     */
    public string $name;
    private string $idToken;

    public function __construct(string $name) {
        $this->name = $name;
        $this->createIdToken();
    }

    //create unique id token
    private function createIdToken(){
        $IdToken = openssl_random_pseudo_bytes(16);
        $IdToken = bin2hex($IdToken);
        $this->idToken = $IdToken;
        //check it the current token is not reserved or taken
    }


    //getter
    public function getIdToken(): string {
        return $this->idToken;
    }
}