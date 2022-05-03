<?php
require_once 'TicTacToe.php';

class Bot{
    private TicTacToe|null $game;
    private array $board;
    private array $config;
    //simulating is going to have 3 states
    //1 - running
    //0 - not running
    //-1 - ended
    private int $simulating;
    private int $depthToSimulateLeft;
    private string $boxes_left;
    private string $idToken;

    public string $name;

    public function __construct($size, $winLength, $depth = 3) {
        $this->game = null;
        $this->board = [];
        $this->config = [
          'size'        => $size,
          'sign'        => '',
          'win_length'  => $winLength,
          'depth'       => $depth
        ];

        $this->depthToSimulateLeft  = $this->config['depth'];
        $this->name                 = 'BOT_00_01';
        $this->idToken              = '777';
        $this->simulating           = 0;
        $this->boxes_left           = 0;
    }

    public function setup($size, $winLength){
        $this->config['size'] = $size;
        $this->config['win_length'] = $winLength;
    }

    public function trigger($board, $sign, $boxesLeft, $game){
        $this->game             = $game;
        $this->board            = $board;
        $this->boxes_left       = $boxesLeft;
        $this->config['sign']   = $sign;

        $this->calculate();
    }

    private function calculate(){
        //Steps set to 1 to multiply with left boxes in depth range
        $steps = 1;
        $winCounter = 0;
        $loseCounter = 0;
        $movesRating = [];
        $simulationCombinationCollection = [];

        //Calculate steps to fetch through
        for($i = 0; $this->config['depth'] > $i; $i++){
            $steps *= $this->boxes_left - $i;
        }

        $this->simulating = 1;
        $freeSquares = [];
        for($i = 0; count($this->board) > $i; $i++){
            if($this->board[$i] === 0) array_push($freeSquares, $i);
        }
        for($i = 0; $this->config['depth'] > $i; $i++){
            $simulationCombination = [];
            for($j = 0; count($freeSquares); $j++){

            }
            array_push($simulationCombinationCollection, $simulationCombination);
        }

        /*
        while($steps > 0){
            for($i = 0; $this->boxes_left > $i; $this->boxes_left--){

            }
        }
        */
        $this->simulating = -1;
    }

    public function getIdToken(): string {
        return $this->idToken;
    }

    public function getDepthToSimulateLeft(): mixed {
        return $this->depthToSimulateLeft;
    }

    public function isSimulating(): int {
        return $this->simulating;
    }
}