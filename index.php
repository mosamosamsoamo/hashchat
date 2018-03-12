<?php
// コメントを変数に代入
$comment = htmlspecialchars($_POST['comment']);

//ハッシュ値をクッキーに登録
if (!isset($_COOKIE['hash'])) {
$shortHash = function ($data, $algo = 'CRC32') {
    return strtr(rtrim(base64_encode(pack('H*', $algo($data))), '='), '+/', '-_');
};
$hashCookie = $shortHash(md5(uniqid()));
setcookie('hash', $hashCookie, time()+60*60*24*7);
}
$hash = $_COOKIE['hash'];

//時間を取得
date_default_timezone_set('Asia/Tokyo');
$time = date("Y/m/d H:i:s");

//データベースに接続
$dsn = 'mysql:dbname=hashchat;host=localhost';
$user = 'root';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->query('SET NAMES utf8');

if ($comment) {
//SQL文を実行
$sql = "INSERT INTO comments SET comment='$comment',hash='$hash', time='$time'";
$stmt = $dbh->prepare($sql);
$stmt->execute();

header('Location: index.php');
}

//一覧SQLを実行
$sql = "SELECT * FROM comments";
$stmt = $dbh->prepare($sql);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Hashchat</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-qrcode"></i> Hashchat</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </div>


  <div class="container">
    <div class="col-lg-offset-2 col-lg-8">

          <?php while (1): ?>
          <?php
          $rec = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($rec == false) {
            break;
          }
          ?>
          <div class="text"><span class="hash"><?php echo $rec['hash']; ?>:&nbsp;</span><?php echo $rec['comment']; ?><span class="date"><?php echo $rec['time'] ?></span></div>
          <?php endwhile; ?>

        <!-- form部分 -->
        <form action="index.php" method="post">
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="comment" class="form-control" id="validate-text" placeholder="Write something..." required>
            </div>
          </div>
        </form>

    </div>
  </div>

<?php
//データベースを切断
$dbh = null;
?>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>