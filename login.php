<?php include "function.php";?>
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
<title>ログイン</title>

</head>
<body>
<?php

if($_POST["post"] == "true"){
//  if($_POST['g-recaptcha-response'] != "" && $_POST["mail"] != "" && $_POST["pass"] != ""){
    $json = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=&response=".$_POST['g-recaptcha-response']));
//    if($json->success == "true"){
      $already = mymysql("vc","vchello")->prepare("SELECT * FROM vc.account WHERE mail = :mail");
      $already->execute(array(":mail"=>$_POST["mail"]));
      $login = $already->fetch();
      if($login["mail"] == $_POST["mail"]){
        if(passcrypt($_POST["pass"]) == $login["pass"]){
          session_start();
          $_SESSION["account"]["id"] = $login["id"];
          alert("info","ログイン完了!id=".$_SESSION["account"]["id"]);
        }else{
          alert_wide("danger","パスワードが違います");
        }
      }else{
        alert_wide("danger","メールアドレスが存在しません。");
      }
//    }
//  }else{
//    alert_wide("danger","入力に不備があります。");
//  }
}

?>
<div class="panel panel-info" style="margin: 30px 10px;">
 <div class="panel-heading">ログイン</div>
 <form method='post' action="./login.php" class="form-horizontal" style="margin: 10px 20px;">
  <div class="form-group">
   <label class="col-sm-2 control-label">メールアドレス</label>
   <div class="col-sm-10"><input type="text" name="mail" class="form-control" placeholder="example@example.com"></input></div>
  </div>
  <div class="form-group">
   <label class="col-sm-2 control-label">パスワード</label>
   <div class="col-sm-10"><input type="password" name="pass" class="form-control" placeholder=""></input></div>
  </div>
  <div class="form-group">
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
   <input type="hidden" name="post" value="true"></input>
   <button type="submit" class="btn btn-info btn-lg">ログイン!</button>
  </div>
 </form>
</div>
<div class="text-center">
 <form method='post' action="./reg.php" class="form-horizontal" style="margin: 10px 20px;">
   <button type="submit" class="btn btn-success btn-lg">新規登録 <form method='post' action="./login.php" class="form-horizontal" style="margin: 10px 20px;"></button>
 </form>
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
