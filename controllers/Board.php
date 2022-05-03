<?php
class Board{
    /*
     * @Update: property size to board renamed and size as board size property added
     */
    private array $board;
    private int $size;

    public function __construct(int $size) {
        $this->size = $size;
        $this->board = [];
        $this->createBoard();
    }
    //Create empty box
    private function createBoard(){
        if(!count($this->board)){
            for($i = 0; $i < $this->size; $i++){
                array_push($this->board, array());
                for($j = 0; $j < $this->size; $j++){
                    array_push($this->board[$i], 0);
                }
            }
        }
    }

    //Translate position from int to XY value and put the sign 1 or 2 in the XY position of the board
    public function checkBox(int $position, int $sign): array|null {
        $row = floor($position / $this->size);
        $column = $position - $row * $this->size;
        if($this->board[$row][$column] === 0){
            $this->board[$row][$column] = $sign;
            //For future usage
            return [
                'row'=> $row,
                'column' => $column
            ];
        }
        else return null;
    }


    public function getBoard() : array{
        return $this->board;
    }
    /*
     * @Update: new method
     */
    public function clearBoard(){
        for($i = 0; count($this->board) > $i; $i++){

            for($j = 0; count($this->board[$i]) > $j; $j++){
                $this->board[$i][$j] = 0;
            }
        }
    }
}