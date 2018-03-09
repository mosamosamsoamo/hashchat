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

//SQL文を実行
$sql = "INSERT INTO comments SET comment='$comment',hash='$hash', time='$time'";
$stmt = $dbh->prepare($sql);
$stmt->execute();

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
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
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
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="index.php" method="post">
          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="comment" class="form-control" id="validate-text" placeholder="Write something..." required>
              <!--<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>-->
            </div>
          </div><br><br>
          <!-- <button type="submit" class="btn btn-primary col-xs-12" disabled>つぶやく</button> -->
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">

          <?php while (1): ?>
          <?php   
  $rec = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($rec == false) {
    break;
  } ?>
          <article class="timeline-entry">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon bg-success">
                      <i class="entypo-feather"></i>
                      <?php echo $rec['id'] ?>
                  </div>
                  <div class="timeline-label">
                      <h2><a href="#">Hash: <?php echo $rec['hash']; ?></a> <span><?php echo $rec['time'] ?></span></h2>
                      <p><?php echo $rec['comment']; ?></p>
                  </div>
              </div>
          </article>
          <?php endwhile; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>
      </div>

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



