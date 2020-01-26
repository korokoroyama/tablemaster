<?php
//★★引数継承
if(empty($_GET["table"])){
}else{
              $table=$_GET["table"];
}

if(empty($_GET["sequence"])){
}else{
              $sequence=$_GET["sequence"];
}
if(empty($_GET["sql_number"])){
	$sql_number=0;
}else{
	$sql_number=$_GET["sql_number"];
}


//★★htmlヘッダ作成
$table='news';
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<title>'.$table."確認ページ:".date("y/m/d")."-".date("H:i").'</title></head>';

/*
<link rel="stylesheet" href="//code.jquery.com/ui/1.8.22/themes/base/jquery-ui.css" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>
<script src="jquery.scrolltable.js"></script>
<script>
$(function(){
	$('.scrollTable').scrolltable({
		stripe: true,
		oddClass: 'odd'
	});
});
</script>
<script type="text/javascript">
function open1() {
    window.open("upsert.php", "hoge", 'width=300, height=400');
}
</script>

<script type="text/javascript">
function open_preview() {
    window.open("about:blank","preview","width=600,height=450,scrollbars=yes");
    document.input_form.target = "preview";
    document.input_form.method = "post";
    document.input_form.action = "upsert.php";
    document.input_form.submit();
}
</script>

<style type="text/css">
	body{ font-size: 0.9em; font-family: Arial, Verdana, sans-serif; color:#555; }
	h1{ margin-top: 0; float: left; }
	#controls{ float: left; padding: 0.3em 1em; }
	table.scrollTable{
		width:100%;
		border:1px solid #ddd000;
	}
	thead{
		background-color: #EEE000;
	}
	thead th{
		border-top:1px solid #FFFF00;
		border-right:1px solid #FFFF00;
		text-align: center;
		padding:0.1em 0.3em;
	}
	tbody td{
		border-top:1px solid #eee;
		border-right:1px solid #eee;
		padding:0.1em 0.3em;
                            }
                            tbody tr.odd td{
                                          background-color: #f9f9f9;
                            }
              </style>
*/
//★★画面内ヘッダ作成
echo "ページ作成日時：".date("Y年m月d日")."-".date("H:i:s")."<br>";

//★★DB接続
require_once('./common.php');
$conn = getConnection();  //←共通ファイルのfunctionが使える

//★★SQLリスト
//$sql[0]='select * from newsbackup';
$sql[0]='select * from news order by getdate DESC';
$sql[1]='select * from news order by getdate';
$sql[2]='select * from news';
for ($i=0 ; $i < count($sql) ;$i++){
	if($i==$sql_number){
		$sqlact=$sql[$i];
		echo "★";
	}
	echo '<a href="newslist.php?sql_number='.$i.'">'.$sql[$i]."</a><br>";
}
echo "<hr>";

//★★SQL実行
$result = pg_query($sqlact);
//echo '<table class="scrollTable" cellpadding="0" cellspacing="0" border="0"><thead>';
echo '<table rules="all"><thead>';

echo '<tr>';
echo "<th>番号</th>";
echo "<th>元サイト</th>";
echo "<th>リンク</th>";
echo "<th>取得日</th>";
//★★個別部分-------------------------------------------------------

//★★整理用--------------------

//★★初期値既定
echo '</tr>';
echo "</thead><tbody>";

//★★カラム作成
for ($x = 0 ; $x < pg_num_rows($result) ; $x++){
	//★レコード情報取得
	$rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
	echo "<tr>";
	//★テーブル作成
	echo '<td>'.($x+1).'</td>';
	echo '<td>'.$rows['site'].'</td>';
	echo '<td><a href="'.$rows['url'].'" target="_blank">',$rows[title],'</a></td>';
	echo '<td>'.$rows['getdate'].'</td>';
	echo "</tr>";
}
echo "</tbody></table>";
//★新規作成
if(0 === strncmp($table, 'm_', 2)){
	echo '<script language="javascript"> ';
	echo 'function test'.$maxid.'() { ';
	echo 'window.open("about:blank","ATMARK","width=600,height=450,scrollbars=yes");';
	echo 'window.document.inform'.$maxid.'.action = "upsert.php" ;'; 
	echo 'window.document.inform'.$maxid.'.target = "ATMARK" ; ';
	echo 'window.document.inform'.$maxid.'.method = "POST" ; ';
	echo 'window.document.inform'.$maxid.'.submit() ; ';
	echo '}';
	echo '</script>';

	echo '<form name="inform'.$maxid.'">';
	echo '<input type="hidden" name="table" value="'.$table.'"/>';
	echo '<input type="hidden" name="id" value="'.$maxid.'"/>';
	echo '<input type="hidden" name="id" value="'.$maxid.'"/>';
	echo '<input type="hidden" name="flag" value="追加準備"/>';
	echo '<input type="button" value="新規追加する" onclick="test'.$maxid.'();">';
	echo '</form> ';
}

echo '<hr>';
//★★テーブル一覧

//-------------------------

//echo "aaaa";
$sqlact1="select relname as tablename from pg_stat_user_tables order by relname";
$result1 = pg_query($sqlact1);
for ($x1 = 0 ; $x1 < pg_num_rows($result1) ; $x1++){
	//★レコード情報取得
	$rows1 = pg_fetch_array($result1, NULL, PGSQL_ASSOC);
	echo '<a href="list.php?table='.$rows1["tablename"].'" target="_blank">'.$rows1["tablename"].'</a>      ';
	echo '<a href="csv.php?table='.$rows1["tablename"].'">'.$rows1["tablename"].'ダウンロード</a><br>';
	
	//★テーブル作成
}

//-----------------------



//★★終了

$close_flag = pg_close($link);
if ($close_flag){
//    print('切断に成功しました。<br>');
}
?>

<?php $features = "width=400, height=300, menubar=no, toolbar=no, scrollbars=yes"; ?>
<a href="list.php" onclick="window.open(this, 'window', <?=$features;?>);return false;">
リンク
</a>

<br>
更新可能テーブルの条件：テーブル名が「m_」から始まること。テーブル名_id というカラムがあり、主キーになっていること。型はint4型で昇順の整数になっていること。
</body>
</html>