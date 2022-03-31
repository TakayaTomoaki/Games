<?php declare(strict_types=1);

session_start();
header('X-Frame-Options: deny');

//SESSION初期化・初期設定
if (empty($_POST)) {
  unset($_SESSION['player']);
  unset($_SESSION['black']);
  unset($_SESSION['white']);

  $mark = "<i class='fa-regular fa-circle' style='color:black'></i>";
  $_SESSION['player'] = 1;
}

//画面遷移を管理（$pageFlag 1:play画面  2:結果画面）
$pageFlag = 1;

//プレイデータ取得
if (!empty($_POST)) {
  $getData = $_POST['record'];
  //$Y=縦, $X=横, $P=player
  [$Y, $X, $P] = explode('-', $getData);
}

//playerの管理
$player = $_SESSION['player'];

if (!empty($P)) {

  if ($player === (int) $P) {
    $player = $player % 2 + 1;
  }

  $_SESSION['player'] = $player;

  if ($player === 1) {
    $mark = "<i class='fa-regular fa-circle' style='color:black'></i>";
  }

  if ($player === 2) {
    $mark = "<i class='fa-solid fa-circle' style='color:black'></i>";
  }
}

//白と黒の棋譜管理
$whiteRecord = [];  //1P
$blackRecord = [];  //2P

//白・黒の棋譜取得
if (!empty($_SESSION['black'])) {
  $blackRecord = $_SESSION['black'];
}

if (!empty($_SESSION['white'])) {
  $whiteRecord = $_SESSION['white'];
}

//指し手の保存
if (!empty($getData)) {
  if ($player === 1) {
    if(empty($whiteRecord[$Y][$X])){
      $blackRecord[$Y][$X] = 1;
      $_SESSION['black'] = $blackRecord;
      $record = $blackRecord;
    }
  }

  if ($player === 2) {
    if(empty($blackRecord[$Y][$X])) {
      $whiteRecord[$Y][$X] = 1;
      $_SESSION['white'] = $whiteRecord;
      $record = $whiteRecord;
    }
  }
}

//五目並んでいるかチェック
//五目並んだ場合、結果画面へ ($pageFlag = 2)
if (!empty($getData) && $pageFlag === 1) {
  $count = 1;
  $checkA = 0;
  $checkB = 0;

  //横1列のチェック
  for ($i = 1; $i < 5; $i++) {
    if (!empty($record[$Y][$X - $i]) && $checkA === 0) {
      $count++;
    } else {
      $checkA = 1;
    }

    if (!empty($record[$Y][$X + $i]) && $checkB === 0) {
      $count++;
    } else {
      $checkB = 1;
    }

    if ($checkA + $checkB === 2 || $count === 5) {
      break;
    }
  }

  if ($count === 5) {
    $pageFlag = 2;
  } else {
    $count = 1;
    $checkA = 0;
    $checkB = 0;
  }


  //縦1列のチェック
  for ($i = 1; $i < 5; $i++) {
    if (!empty($record[$Y - $i][$X]) && $checkA === 0) {
      $count++;
    } else {
      $checkA = 1;
    }

    if (!empty($record[$Y + $i][$X]) && $checkB === 0) {
      $count++;
    } else {
      $checkB = 1;
    }

    if ($checkA + $checkB === 2 || $count === 5) {
      break;
    }
  }

  if ($count === 5) {
    $pageFlag = 2;
  } else {
    $count = 1;
    $checkA = 0;
    $checkB = 0;
  }

  //斜め右上りのチェック
  for ($i = 1; $i < 5; $i++) {
    if (!empty($record[$Y - $i][$X - $i]) && $checkA === 0) {
      $count++;
    } else {
      $checkA = 1;
    }

    if (!empty($record[$Y + $i][$X + $i]) && $checkB === 0) {
      $count++;
    } else {
      $checkB = 1;
    }

    if ($checkA + $checkB === 2 || $count === 5) {
      break;
    }
  }

  if ($count === 5) {
    $pageFlag = 2;
  } else {
    $count = 1;
    $checkA = 0;
    $checkB = 0;
  }

  //斜め左上りのチェック
  for ($i = 1; $i < 5; $i++) {
    if (!empty($record[$Y - $i][$X + $i]) && $checkA === 0) {
      $count++;
    } else {
      $checkA = 1;
    }

    if (!empty($record[$Y + $i][$X - $i]) && $checkB === 0) {
      $count++;
    } else {
      $checkB = 1;
    }

    if ($checkA + $checkB === 2 || $count === 5) {
      break;
    }
  }

  if ($count === 5) {
    $pageFlag = 2;
  } else {
    $count = 1;
    $checkA = 0;
    $checkB = 0;
  }
}

//結果画面の表示処理
if ($pageFlag === 2) {
  if ($P === "1") {
    $pMark = "<i class='fa-regular fa-circle' style='color:black'></i>";
  }

  if ($P === "2") {
    $pMark = "<i class='fa-solid fa-circle' style='color:black'></i>";
  }
}

$error = error_get_last();
if(!empty($error)){
  $pageFlag = 2;
  $errorMessage = "エラーが発生しました。初めからやり直して下さい。";
}

?>



<!DOCTYPE html>

<head>
  <meta charset="utf-8">
  <title>五目並べ</title>
  <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<style>
  table {
    width: 640px;
    height: 640px;
    border: 1px solid #333;
  }

  tr {
    text-align: center;
  }

  td {
    border: 1px solid #333;
  }

  th {
    background-color: #333;
    color: #fff;
    width: 6.25%;
  }

  a {
    text-decoration: none;
    color: white;
  }

  .btn {
    color: white;
    background: transparent;
    border: none;
  }
</style>

<body>
<!--play画面（$pageFlag=1）-->
<?php if ($pageFlag === 1) : ?>
  <?php echo $player."Ｐ【 ".$mark." 】のターンです"; ?>
  <table>
    <?php
    for ($y = 0; $y < 16; $y++) {
      echo "<tr>"."<th>".$y."</th>";

      for ($x = 1; $x < 16; $x++) {
        if ($y === 0) {
          echo "<th>".$x."</th>";
        }

        if ($y !== 0) {
          //棋譜配列のvalue有無により表示切替
          if (!empty($whiteRecord[$y][$x])) {
            echo "<td><i class='fa-regular fa-circle' style='color:black'></i></td>";
          } elseif (!empty($blackRecord[$y][$x])) {
            echo "<td><i class='fa-solid fa-circle' style='color:black'></i></td>";
          } else {
            echo "<td><form action='' method='post'><button class='btn' name='record' type='submit' value='$y-$x-$player'>・</button></form></td>";
          }
        }
      }
      echo "</tr>";
    }
    ?>
  </table>
  <br>
  <form method="get" action="Gomokunarabe.php">
    <button>リセットする</button>
  </form>
<?php endif; ?>


<!--結果画面（$pageFlag=2）-->
<?php if ($pageFlag === 2) : ?>
  <?php if(empty($error)) {
    echo $P."Ｐ【 ".$pMark." 】の 勝利 です！";
  } else {
    echo $errorMessage;
  }
  ?>
  <table>
    <?php
    for ($y = 0; $y < 16; $y++) {
      echo "<tr>"."<th>".$y."</th>";

      for ($x = 1; $x < 16; $x++) {
        if ($y === 0) {
          echo "<th>".$x."</th>";
        }

        if ($y !== 0) {
          //棋譜配列のvalue有無により表示切替
          if (!empty($whiteRecord[$y][$x])) {
            echo "<td><i class='fa-regular fa-circle' style='color:black'></i></td>";
          } elseif (!empty($blackRecord[$y][$x])) {
            echo "<td><i class='fa-solid fa-circle' style='color:black'></i></td>";
          } else {
            echo "<td> </td>";
          }
        }
      }
      echo "</tr>";
    }
    ?>
  </table>
  <br>
  <?php
  unset($_SESSION['player']);
  unset($_SESSION['black']);
  unset($_SESSION['white']);
  ?>
  <form method="get" action="Gomokunarabe.php">
    <button>もう一度プレイする</button>
  </form>
<?php endif; ?>

</body>
