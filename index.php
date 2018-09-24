<?php include "function.php"; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5sshiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<? /*<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<!-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" /> -->
*/ ?>

<script src="https://www.google.com/recaptcha/api.js"></script>
<title>取引練習場</title>

</head>
<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
<?php /*      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
        <span class="sr-only">メニュー</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>*/ ?>

      <a class="navbar-brand" href="./index.php">通貨取引練習所</a>
    </div>
<?php /*    <div class="collapse navbar-collapse" id="navbar1">
      <ul class="nav navbar-nav">
        <li><a href="#">asdfg</a></li>
      </ul>
    </div> */ ?>
  </div>
</nav>
<div class="text-center">
  <button class="btn btn-success" type="button">VTC残高<span class="badge">0VTC</span></button><button class="btn btn-info" type="button">Vona残高<span class="badge">0Vona</span></button>
<?php
session_start();
if($_SESSION["account"]["id"]) echo "<form method='post' action='./logout.php'><button type='submit' class='btn btn-danger btn-lg'>ログアウト</button></form>";
?>
</div>

<?php
if($_POST["maked"] == "maked" && $_SESSION["account"]["id"]){
  if($_POST['g-recaptcha-response'] != ""){
    $json = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LerDBkTAAAAAEXXBVxriGhZQbGR51o0xMJjLviS&response=".$_POST['g-recaptcha-response']));
//    if($json->success == "true"){
    if(true){
      if($_POST["rate"] == "" | $_POST["total"] == "" | $_POST["buysell"] == ""){
        alert_wide("danger","入力に不備があります。");
      }else{
        $_POST["rate"] = floatval($_POST["rate"]);
        $_POST["rate"] = $_POST["rate"]*100000000;
        $_POST["rate"] = floor($_POST["rate"]);
        if($_POST["buysell"] == "1"){
          $buysell = "売る";
          $gyaku = 2;
        }else{
          $buysell = "買う";
          $gyaku = 1;
        }
        $kakunin = mymysql("vc","vchello")->prepare("SELECT rate,total,owner FROM vc.trade WHERE type = :type and rate = :rate ORDER BY maketime ASC LIMIT 1");
        $kakunin->execute(array(":type"=>$gyaku,":rate"=>$_POST["rate"]));
        $butukeru = $kakunin->fetch();


        if($butukeru["rate"] == $_POST["rate"]){
          if($butukeru["total"] > $_POST["total"]){
            $sa = $butukeru["total"] - $_POST["total"];

            $ban = mymysql("vc","vchello")->prepare("UPDATE vc.trade SET total = :sa WHERE rate = :rate AND type = :type ORDER BY maketime ASC LIMIT 1");
            $ban->execute(array(":sa"=>$sa,":rate"=>$_POST["rate"],":type"=>$gyaku));

            $kousyou = mymysql("vc","vchello")->prepare("INSERT INTO vc.trade (rate,total,maketime,type,owner) VALUES(:rate,:total,:maketime,:type,0)");
            $kousyou->execute(array(":rate"=>$_POST["rate"],":total"=>$_POST["total"],":maketime"=>time(),":type"=>$gyaku+2));

            alert("info","板をぶつけました。(make勝ち)");
          }elseif($butukeru["total"] < $_POST["total"]){
            alert("info","注文に失敗しました。makeよりも大きい量のtakeに対応していません。");
          }else{
            $_POST["buysell"] = $gyaku+2;
            $ban = mymysql("vc","vchello")->prepare("UPDATE vc.trade SET type = :type , maketime = :time WHERE rate = :rate AND type = :gyaku ORDER BY maketime ASC LIMIT 1");
            $ban->execute(array(":type"=>$_POST["buysell"],":rate"=>$_POST["rate"],":gyaku"=>$gyaku,":time"=>time()));
            alert("info","板をぶつけました。(引き分け)");
          }
        }else{
          $tyumon = mymysql("vc","vchello")->prepare("INSERT INTO vc.trade (total,rate,type,maketime,owner)  VALUES (:total,:rate,:type,:time,:owner)");
          $tyumon->execute(array(":total"=>$_POST["total"],":rate"=>$_POST["rate"],":type"=>$_POST["buysell"],":time"=>time(),":owner"=>$_SESSION["account"]["id"]));
          alert("info","注文しました。");
        }
      }
    }
  }else{
    alert_wide("danger","ReCaptcha認証をしてください。");
  }
}
?>
<hr>

<div class="panel panel-info" style="margin: 30px 10px;">
  <div class="panel-heading">注文一覧</div>
  <div class="table-responsive">
  <table class="table">

    <tr><th>　</th><th>取引額</th><th>レート</th><th>種類(Vonacoinを)</th></tr>
<?php

$trade = mymysql("vc","vchello")->query("SELECT rate,total,type FROM vc.trade WHERE type = 1 ORDER BY rate DESC");
for($i = 0;$row = $trade->fetch();$i++){
  if($selllist[$i-1]["rate"] == $row["rate"]){
    $selllist[$i-1]["total"] += $row["total"];
    $i--;
  }else{
    $selllist[$i]["total"] = $row["total"];
    $selllist[$i]["rate"] = $row["rate"];
  }
}


$trade = mymysql("vc","vchello")->query("SELECT rate,total,type FROM vc.trade WHERE type = 2 ORDER BY rate DESC");
for($i = 0;$row = $trade->fetch();$i++){
  if($buylist[$i-1]["rate"] == $row["rate"]){
    $buylist[$i-1]["total"] += $row["total"];
    $i--;
  }else{
    $buylist[$i]["total"] = $row["total"];
    $buylist[$i]["rate"] = $row["rate"];
  }
}

$c = count($selllist);
for($i = 0;$i < $c && $i < 10 ;$i++){
    echo "<tr><td class='success'>　</td><td>合計";
    echo $selllist[$i]["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $selllist[$i]["rate"]/100000000;
    echo "VTCで</td><td style='color:#55ff55;'>売る</td></tr>";
}

$c = count($buylist);
for($i = 0;$i < $c && $i < 10;$i++){
    echo "<tr><td class='danger'>　</td><td>合計";
    echo $buylist[$i]["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $buylist[$i]["rate"]/100000000;
    echo "VTCで</td><td style='color:#ff5555;'>買う</td></tr>";
}

?>

  </table>
  </div>
  <div class="panel-footer text-right"><a href="./all.php">すべて→</a></div>
</div>

<div class="panel panel-info" style="margin: 30px 10px;">
  <div class="panel-heading">注文</div>
<?php
//session_start();
if(!($_SESSION["account"]["id"])){
  alert_wide("danger","ログインしてください。");
  echo "<div class='text-center'><form method='post' action='./login.php'><button type='submit' class='btn btn-success btn-lg'>ログイン</button></form></div>";
}else{
  echo <<< END
  <form method='post' action="./index.php" class="form-horizontal" style="margin: 10px 20px;">

<div class="form-group">
 <label class="col-sm-2 control-label">1Vonaあたり(単位:VTC)</label>
 <div class="col-sm-10"><input type="text" name="rate" class="form-control" placeholder="1Vonaあたりの価格(VTC)"></input></div>
</div>
<div class="form-group">
 <label class="col-sm-2 control-label">全部で(単位:Vona)</label>
 <div class="col-sm-10"><input type="text" name="total" class="form-control" placeholder="合計で注文する量(Vona)"></input></div>
</div>
<div class="form-group">
 <div class="radio"><label><input type="radio" name="buysell" value="1" checked="checked">Vonacoinを売る</input></label></div>
 <div class="radio"><label><input type="radio" name="buysell" value="2">Vonacoinを買う</input></label></div>
</div>

<div class="form-group text-center">
      <div class="g-recaptcha col-sm-10" data-theme="light" data-size="compact" data-sitekey="6LerDBkTAAAAAOn5dBGanqRMKGi3JQDiMfd2kC_s"></div>
      <noscript>
        <div style="width: 302px; height: 352px;">
          <div style="width: 302px; height: 352px; position: relative;">
            <div style="width: 302px; height: 352px; position: absolute;">
              <iframe src="https://www.google.com/recaptcha/api/fallback?k=6Ldw2BATAAAAAGRgvi82jAqf-ZaJ_35gzXtxAZdT" frameborder="0" scrolling="no" style="width: 302px; height:352px; border-style: none;"></iframe>
            </div>
            <div style="width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;">
             <textarea style="color:#000000" id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response"style="width: 250px; height: 80px; border: 1px solid #c1c1c1;margin: 0px; padding: 0px; resize: none;" value=""></textarea>
            </div>
          </div>
        </div>
      </noscript>
</div>
      <div class="form-group text-center">
        <input type="hidden" name="maked" value="maked"></input>
        <button type="submit" class="btn btn-success btn-lg">送信!</button>
      </div>
    </form>
END;
}?>
  </div>
</div>

<div class="panel panel-info" style="margin: 30px 10px;">
  <div class="panel-heading">取引履歴</div>
  <div class="table-responsive">
    <table class="table table-hover">
      <tr><th>　</th><th>取引額</th><th>レート</th><th>makeの種類(Vonacoinを)</th></tr>

<?php
$trade = mymysql("vc","vchello")->query("SELECT rate,total,type,maketime FROM vc.trade WHERE type = 3 OR type = 4 ORDER BY maketime DESC LIMIT 15");
/*for($i = 0;$row = $trade->fetch();$i++)
    $klist[$i]["total"] = $row["total"];
    $klist[$i]["rate"] = $row["rate"];
    $klist[$i]["type"] = $row["type"];
    $klist[$i]["maketime"] = $row["maketime"];
}
$c = count($klist);*/
for($i = 0;$row = $trade->fetch();$i++){
    if($row["type"] == 3){
      $row["type"] = "売り";
    }else{
      $row["type"] = "買い";
    }

    echo "<tr><td class='success'>　</td><td>合計";
    echo $row["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $row["rate"]/100000000;
    echo "VTCで</td><td style='color:#5555ff;'>".$row["type"]."</td></tr>";
} ?>
    </table>
  </div>
  <div class="panel-footer text-right"><a href="./all.php">すべて→</a></div>
</div>

<hr>
広告
<iframe data-aa='145918' src='https://ad.a-ads.com/145918?size=234x60' scrolling='no' style='width:234px; height:60px; border:0px; padding:0;overflow:hidden' allowtransparency='true' frameborder='0'></iframe>

<!-- Tag START-->
<script type="text/javascript"><!--
document.write( "<scr"+"ipt type=text/javascript src=\""+(document.location.protocol.indexOf("https")!=-1?"https://www.kaiseki-website.com":"http://www.kaiseki-website.com")+"/getstats.js.php?sid=1172222&linkid=2343_&guid=ON&random="+(Math.random()*9999999)+"\"></scri"+"pt>" );
//--></script>
<a href="http://www.oms-hk.com/show.php/itemid/T246/" target="_blank">リファークリーム</a>
<noscript><img src="http://www.kaiseki-website.com/getstats_m.php?sid=1172222&guid=ON" /></noscript>
<!-- Tag END-->

</body>
</html>
