<?php

use JetBrains\PhpStorm\Pure;

require_once 'Board.php';
require_once 'Bot.php';

class TicTacToe {
    /*
     * @Update: added new private properties 'players token, winner, loser, winLength, winBoxes and currentPlayer', renamed size to board
     */
    private Board $board;
    private string $token;
    private string $winner;
    private string $loser;
    private array $players;
    private int $winLength;
    private array $winBoxes;
    public int $size;

    private Bot|Player $currentPlayer;

    public function __construct(array $players, int $size = 3, int $winLength = 3) {
        $this->size = max(min($size , 10), 3);
        $this->players = $players;
        $this->winLength = $winLength;
        $this->token = '';
        $this->winBoxes = [];
        $this->winner = '';
        $this->loser = '';

        $this->board = new Board($this->size);
        $this->currentPlayer = $this->players[0];
        $this->createToken();

    }

    /*
     * @new: new getters
     */
    public function getCurrentPlayer(): Bot|Player {
        return $this->currentPlayer;
    }
    public function getPlayers(): array {
        return $this->players;
    }
    public function getToken(): string {
        return $this->token;
    }
    /*
     * @Update: new method
     */

    private function createToken(){
        $token = openssl_random_pseudo_bytes(16);
        $token = bin2hex($token);
        $this->token = $token;
        //check it the current token is not reserved or something
    }

    /*
     * @Update: removed unnecessary method
     *
     *  public function join(Player $player){}
     */


    /*
     * @Update: changed position type
     */
    //the simulation property is going to be a key accessor for bot simulations
    public function makeMove(int $position): null|bool{

        //1 stands for X and 2 for O
        $sign = $this->currentPlayer === $this->players[0]->getIdToken() ? 1 : 2;
        $transformedPosition = $this->board->checkBox($position, $sign);
        $boxesLeft = 0;

        $board = $this->board->getBoard();
        for($i = 0; $i <$this->size; $i++){
            for($j = 0; $j < $this->size; $j++){
                if($board[$i][$j] === 0) $boxesLeft++;
            }
        }
        //against cross site scripting
        print_r($this->currentPlayer);
        if($this->currentPlayer instanceof Bot && $this->currentPlayer->isSimulating() === 0) $this->currentPlayer->trigger($this->board->getBoard(), $sign, $boxesLeft, $this);
        elseif($this->currentPlayer instanceof Bot && $this->currentPlayer->isSimulating() === 1) return $this->checkWin($transformedPosition, $sign);
        else{
            if($transformedPosition === null) return null;

            if($this->checkWin($transformedPosition, $sign) === true) $this->end();
            else if($boxesLeft === 0) $this->restart();

            $this->togglePlayer();
        }
        return null;
    }

    /*
     * @Update: public to renamed, added new parameter
     */
    private function checkWin($transformedPosition, $sign): bool {
        //How the toCheck array is going to look
        //example:
        // __________
        //|x  1  2   |
        //|1  1      |
        //|2     2   |
        //|__________|
        $checkLength = $this->winLength - 1;
        $winLength = $this->winLength;
        $row = $transformedPosition['row'];
        $column = $transformedPosition['column'];
        $board = $this->board->getBoard();
        $toCheck = [
            'row' => [],
            'column' => [],
        ];

        //check if the counting step is in the board range to determine possible match up with neighbour fields
        for($i = -$checkLength; $i <= $checkLength; $i++){
            if(isset($board[$row + $i])) array_push($toCheck['row'], $i + $row);
            if(isset($board[$column + $i])) array_push($toCheck['column'], $i + $column);
        }

        //The idea behind those functions is to make clear structure of orthogonal and diagonal win checkers with winning fields array as return
        //which after is going to be stored into the class $winBoxes array if not empty
        //Also win arrays of the functions return all win fields, even if there is multiple win conditions occurred
        $orthogonalFunc = function() use ($board, $row, $column, $sign, $toCheck, $winLength){
            $win1 = $win2 = 0;
            $winBoxes = [];
            //Save last XY of win fields and count down || count up them in the orthogonal steps
            $rowToCountFrom = $columnToCountFrom = 0;

            //COLUMN check
            foreach($toCheck['row'] as $value) {
                if($board[$value][$column] === $sign) {
                    $rowToCountFrom = $value;
                    ++$win1;
                }
                else $win1 = $win1 >= $winLength ? $win1 : 0;
            }
            if($win1 >= $winLength) {
                for($i = 0; $i < $win1; $i++) array_push($winBoxes, [$rowToCountFrom - $i, $column]);
            }
            //ROW check
            foreach($toCheck['column'] as $value) {
                if($board[$row][$value] === $sign) {
                    ++$win2;
                    $columnToCountFrom = $value;
                }
                else $win2 = $win2 >= $winLength ? $win2 : 0;
            }
            if($win2 >= $winLength) {
                for($i = 0; $i < $win2; $i++) array_push($winBoxes, [$row, $columnToCountFrom - $i]);
            }
            return [
                'win' =>$win2 >= $winLength || $win1 >= $winLength,
                'arr' => $winBoxes
            ];
        };

        $diagonalFunc = function() use ($board, $row, $column, $sign, $toCheck, $winLength, $checkLength){
            $win1 = $win2 = 0;
            $winBoxes = [];
            $row -= $checkLength;
            $column1 = $column - $checkLength;
            $column2 = $column + $checkLength;
            //Save last XY of win fields and count down || count up them in the diagonal steps
            $rowToCountFrom = $column1ToCountFrom = $column2ToCountFrom = 0;
            for($i = 0; $i < ($winLength * 2) - 1; $row++, $column1++, $column2--, $i++){
                if(in_array($row, $toCheck['row'])){
                    //DIA1 from top left -> bottom right
                    if(in_array($column1, $toCheck['column'])){
                        if($board[$row][$column1] === $sign){
                            $win1++;
                            $rowToCountFrom = $row;
                            $column1ToCountFrom = $column1;
                        }
                        else $win1 = $win1 >= $winLength ? $win1 : 0;
                    }
                    //DIA1 from top right -> bottom left
                    if(in_array($column2, $toCheck['column'])){
                        if($board[$row][$column2] === $sign){
                            $win2++;
                            $rowToCountFrom = $row;
                            $column2ToCountFrom = $column2;
                        }
                        else $win2 = $win2 >= $winLength ? $win2 : 0;
                    }
                }
            }
            if($win1 >= $winLength){
                for($j = 0; $j < $win1 ; $j++){
                    array_push($winBoxes, [$rowToCountFrom - $j, $column1ToCountFrom - $j]);
                }
            }
            if($win2 >= $winLength) {
                for($j = 0; $j < $win2; $j++){
                    array_push($winBoxes, [$rowToCountFrom - $j, $column2ToCountFrom + $j]);
                }
            }
            return [
                'win' =>$win2 >= $winLength || $win1 >= $winLength,
                'arr' => $winBoxes
            ];

        };
        $diagonalFuncReturn = $diagonalFunc();
        $orthogonalFuncReturn = $orthogonalFunc();

        if($orthogonalFuncReturn['win'] === true) foreach($orthogonalFuncReturn['arr'] as $values) array_push($this->winBoxes, $values);
        if($diagonalFuncReturn['win'] === true) foreach($diagonalFuncReturn['arr'] as $values) array_push($this->winBoxes, $values);
        /*
        echo '<pre>';
        print_r($this->winBoxes);
        echo '</pre>';
        */
        return count($this->winBoxes) > 0;
    }

    private function togglePlayer(){
        $player1 = $this->players[0];
        $player2 = $this->players[1];
        $this->currentPlayer = $this->currentPlayer === $player1 ? $player2 : $player1;
    }

    #[Pure] public function draw(): string{
        $board = $this->board->getBoard();
        $output = '';
        $sign = $this->currentPlayer === $this->players[0] ? 'X' : 'O';

        //If the winner is determined put a random winning message
        if($this->winner !== ''){
            $textArr = [
                '<p style="text-align: center">' . $this->winner . ' violated ' . $this->loser . '</p>',
                '<p style="text-align: center">' . $this->winner . ' put down ' . $this->loser . ' on its '. $sign .'\'s </p>',
                '<p style="text-align: center">' . $this->winner . ' broke ' . $this->loser . '\'s anckles </p>',
                '<p style="text-align: center"> As I say in russian, ' . $this->winner . ' showed ' . $this->loser . ' where crawfishes hibernate</p>',
                '<p style="text-align: center">' . $this->winner . ' became Khabib and saw McGregor in ' . $this->loser . '</p>',
            ];
             $output .= $textArr[array_rand($textArr)];
        }
        //Fetch through any board field and check for value
        for($i = 0; $this->size > $i; $i++){
            $output .= '<tr>';
            for($j = 0; $this->size > $j; $j++){
                switch($board[$i][$j]){
                    case 0:
                        //Not selected
                        //The name is integer typed square position which is going be translated to XY coordinates in Board class
                        if($this->winner === '') $output .= '<td><input type="submit" class="reset field" name="'. (($i * $this->size) + $j) .'" value="'.$sign.'" /></td>';
                        else $output .= '<td><span class="reset field"></span></td>';
                        break;
                    case 1:
                        //X
                        //if the field XY coordinates are in win boxes array put extra 'win' class
                        if(in_array([$i, $j], $this->winBoxes))$output .= '<td class="colorX win"><span class="field"> X </span></td>';
                        else $output .= '<td><span class="field colorX"> X </span></td>';
                        break;
                    case 2:
                        //O
                        if(in_array([$i, $j], $this->winBoxes))$output .= '<td class="colorO win"><span class="field"> O </span></td>';
                        else $output .= '<td><span class="field colorO"> O </span></td>';
                    break;
                }
            }
            $output .= '</tr>';
        }
        return $output;
    }

    /*
     * @Update: public to private
     */
    private function end(){
        $this->winner = $this->currentPlayer === $this->players[0] ? $this->players[0] : $this->players[1];
        $this->loser = $this->winner === $this->players[0] ? $this->players[0] : $this->players[1];
    }
    //Reset values and switch players
    public function restart(){
        $this->board->clearBoard();
        $this->winBoxes = [];
        $this->winner = $this->loser = '';
        $this->players = array_reverse($this->players);
        $this->currentPlayer = $this->players[0];
        //additional restart config
    }
}

