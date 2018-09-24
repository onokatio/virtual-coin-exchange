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
<title>全注文</title>

</head>
<body>

<div class="panel panel-info" style="margin: 30px 10px;">
  <div class="panel-heading">すべての注文一覧</div>
  <div class="table-responsive">
  <table class="table">

    <tr><th>　</th><th>取引額</th><th>レート</th><th>種類(Vonacoinを)</th></tr>

<?php

$trade = mymysql("vc","vchello")->query("SELECT rate,total,type FROM vc.trade WHERE type = 1 ORDER BY rate DESC");
for($i = 0;$row = $trade->fetch();$i++){
    echo "<tr><td class='success'>　</td><td>合計";
    echo $row["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $row["rate"]/100000000;
    echo "VTCで</td><td style='color:#55ff55;'>売る</td></tr>";
}

$trade = mymysql("vc","vchello")->query("SELECT rate,total,type FROM vc.trade WHERE type = 2 ORDER BY rate DESC");
for($i=0;$row = $trade->fetch();$i++){
    echo "<tr><td class='danger'>　</td><td>合計";
    echo $row["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $row["rate"]/100000000;
    echo "VTCで</td><td style='color:#ff5555;'>買う</td></tr>";
}

?>

  </table>
  </div>
</div>

<div class="panel panel-info" style="margin: 30px 10px;">
  <div class="panel-heading">すべての取引履歴</div>
  <div class="table-responsive">
    <table class="table table-hover">
      <tr><th>　</th><th>取引額</th><th>レート</th><th>種類(Vonacoinを)</th><th>時刻</th></tr>

<?php
$trade = mymysql("vc","vchello")->query("SELECT rate,total,type,maketime FROM vc.trade WHERE type = 3 OR type = 4 ORDER BY maketime DESC");
for(;$row = $trade->fetch();){
    if($row["type"] == 3){
      $row["type"] = "売り";
    }else{
      $row["type"] = "買い";
    }

    echo "<tr><td class='success'>　</td><td>合計";
    echo $row["total"];
    echo "Vonaを</td><td>1VONAあたり";
    echo $row["rate"]/100000000;
    echo "VTCで</td><td style='color:#5555ff;'>".$row["type"]."</td>";
    echo "<td>".date("Y/m/d　H:i:s",$row["maketime"])."</td></tr>";
} ?>
    </table>
  </div>
</div>

<!-- Tag START-->
<script type="text/javascript"><!--
document.write( "<scr"+"ipt type=text/javascript src=\""+(document.location.protocol.indexOf("https")!=-1?"https://www.kaiseki-website.com":"http://www.kaiseki-website.com")+"/getstats.js.php?sid=1172222&linkid=2343_&guid=ON&random="+(Math.random()*9999999)+"\"></scri"+"pt>" );
//--></script>
<a href="http://www.oms-hk.com/show.php/itemid/T246/" target="_blank">リファークリーム</a>
<noscript><img src="http://www.kaiseki-website.com/getstats_m.php?sid=1172222&guid=ON" /></noscript>
<!-- Tag END-->

</body>
</html>
