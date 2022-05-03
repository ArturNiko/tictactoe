<?php
define ('BASEPATH', realpath(dirname(__FILE__)));
require_once (BASEPATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

session_start();
//session_destroy();
//if game is running
if(isset($_SESSION['game'])) {
    $game = $_SESSION['game'];
    //read field name of the clicked field and pass it to game make move function
    if(isset($_POST)){
        if(isset($_POST['reset'])){
            $game->restart();
        }
        else{
            for($i = 0; pow($game->size, 2) > $i; $i++){
                if(isset($_POST[$i])){
                    $game->makeMove($i);
                }
            }
        }
    }
}
//create new game
else {

    $size = 3;
    $winLength = 3;

    $players = [
        new Player('dimitri'),
        new Bot($size, $winLength, 3)
    ];
    $game = new TicTacToe($players, $size, $winLength);
    //Maybe serialize if object is incomplete
    $_SESSION['game'] = $game;
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tic-Tac-Toe</title>
    <meta name="description" content="Tic-Tac-Toe-Game. Here is a short description for the page. This text is displayed e. g. in search engine result listings.">
    <!-- Bootstrap -->
    <link href=" ./css/app.css" rel="stylesheet">
    <style rel="stylesheet">
        :root{
            --box-shadow: 10px 10px 15px;
        }
        *{
            font-family: Arial, serif;
        }

        header{
            z-index: 2 !important;
            background-color: #f9f9f9 !important;
            height: 80px;
        }
        @supports ((-webkit-backdrop-filter: none) or (backdrop-filter: none)) {
            header{
                background-color: #f9f9f9c0 !important;
                -webkit-backdrop-filter: blur(5px);
                backdrop-filter: blur(5px);
            }
        }
        .esfl-logo{
            height: 60px;
            width: 60px;
        }


        main{
            z-index: 1 !important;
            margin-top: 100px;
        }
        table.tic td{
            /*border: 1px solid;*/
            -webkit-box-shadow: var(--box-shadow) -5px rgba(200,200,200,0.73);
            box-shadow: var(--box-shadow) -5px rgba(200,200,200,0.73);
            height: 7em;
            min-height: 70px;
            width: 7em;
            min-width: 70px;
            /*vertical-align: center; */
            text-align: center;
        }
        table.tic .field{
            font-size: 4em;
        }

        table.tic .reset{
            border: none;
            background-color: transparent;
            opacity: 0;
        }
        input.reset:hover {
            opacity: 1;
            color: <?= $game->getCurrentPlayer() === $game->getPlayers()[0] ? '#5716c8' : '#c81657' ?>; /* red on hover */
        }
        .colorX { color: #77e; } /* X is light red */
        .colorO { color: #e77; } /* O is light blue */
        .colorX.win {animation: animWinCross .4s forwards;}
        .colorO.win {animation: animWinCirc .4s forwards;}

        @keyframes animWinCirc{
            from {
                color: #e77;
                background-color: #fff;
                -webkit-box-shadow: var(--box-shadow) -5px rgba(200,200,200, 0.73);
                box-shadow: var(--box-shadow) -5px rgba(200,200,200,0.73);
            }
            to {
                color: #fff;
                background-color: #f99;
                -webkit-box-shadow: var(--box-shadow) rgba(255,100,100,0.73);
                box-shadow: var(--box-shadow) rgba(255,100,100,0.73);
            }
        }
        @keyframes animWinCross{
            from {
                color: #77e;
                background-color: #fff;
                -webkit-box-shadow: var(--box-shadow) -5px rgba(200,200,200, 0.73);
                box-shadow: var(--box-shadow) -5px rgba(200,200,200,0.73);
            }
            to {
                color: #fff;
                background-color: #99f;
                -webkit-box-shadow: var(--box-shadow) 2px rgba(100,100,255,0.73);
                box-shadow: var(--box-shadow) 2px rgba(100,100,255,0.73);
            }
        }
    </style>
</head>
<body class="">
    <header class="card-header d-flex align-items-center w-100 top-0 shadow position-fixed">
        <a class="navbar-brand" href="index.php"><img src="img/logo_eckener_schule.gif" class="esfl-logo" alt="ESFL-Logo"/></a>
        <nav class="collapse-horizontal">
                <ul class="nav navbar-nav d-flex flex-row">
                    <li class="px-2 border-start border-end d-flex align-items-center"><p class="my-0">Tic-Tac-Toe</p></li>
                    <li class="px-2 border-end">
                        <form action="" method="post">
                            <input class="btn btn-outline-primary" type="submit" name="reset" value="Spiel neustarten">
                        </form>
                    </li>
                    <li class="px-2 border-end">
                        <button class="btn btn-outline-primary" onclick="window.open('https://de.wikipedia.org/wiki/Tic-Tac-Toe', '_blank')">
                            Über Tic-Tac-Toe
                        </button>
                    </li>
                </ul>
        </nav>
    </header>
    <main class="container bg-colorWhite mb-5">
        <h1 class="bg-colorESFLBlau">Hi, Flensburg developers!</h1>
        <h3>Let's <span style="color: #99f">X</span><span style="color: #f99">O</span> it</h3>
        <button class="btn btn-primary my-3" data-bs-toggle="collapse" data-bs-target="#infotext" aria-expanded="true" aria-controls="infotext">
            <span class="glyphicon glyphicon-menu-up"></span> Infotext minimieren/maximieren.
        </button>
        <div id="infotext" class="collapse show card card-body col-12">
            <!-- wrapper to prevent flexbox collapse bag -->
            <div id="wrapper" class="d-flex flex-row gap-3">
                <div class="float-start">
                    <a title="By Thomas Steiner [GFDL (http://www.gnu.org/copyleft/fdl.html) or CC-BY-SA-3.0 (http://creativecommons.org/licenses/by-sa/3.0/)], via Wikimedia Commons" href="https://commons.wikimedia.org/wiki/File%3ATictactoe1.gif" target="_blank">
                        <img alt="Tictactoe1" src="https://upload.wikimedia.org/wikipedia/commons/3/33/Tictactoe1.gif" class="img-responsive img-rounded"/>
                    </a>
                </div>
                <div class="float-end">
                    <p>Tic-Tac-Toe (auch: Drei gewinnt, Kreis und Kreuz, Dodelschach) ist ein klassisches,
                        einfaches Zweipersonen-Strategiespiel, dessen Geschichte sich bis ins 12. Jahrhundert v. Chr. zurückverfolgen lässt...
                        <br/>
                        <small>
                            <a href="https://en.wikipedia.org/wiki/Tic-tac-toe" target="_blank">(bei Wikipedia weiterlesen...)</a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
        <section id="gameWrapper" class="d-flex align-items-center flex-column mt-4">
            <h1>Tic-Tac-Toe</h1>
            <form method="post" id="ticTacToeForm" action="#game">
                <table class="tic" id="game">
                    <?= $game->draw() ?>
                </table>
            </form>
        </section>
    </main>
    <footer class="container bg-colorESFLGelb">
    </footer>
    <script src="./js/app.js" type="module"></script>
</body>
</html>
