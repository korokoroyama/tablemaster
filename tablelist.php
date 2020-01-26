<?php
//★★引数継承
if(!empty($_GET["table"])){
	$table=$_GET["table"];
}

if(!empty($_GET["sequence"])){
	$sequence=$_GET["sequence"];
}

//★★htmlヘッダ作成
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<title>'.$table."確認ページ:".date("y/m/d")."-".date("H:i").'</title></head>';
?>
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
<?php
//★★画面内ヘッダ作成
echo "ページ作成日時：".date("Y年m月d日")."-".date("H:i:s")."<br>";

//★★DB接続
//共通ファイルの読み込み
require_once('./common.php');
 
//コネクション取得
$conn = getConnection();  //←共通ファイルのfunctionが使える

$sqlact1="select relname as tablename from pg_stat_user_tables order by relname";
$result1 = pg_query($sqlact1);
echo '<table border="1" width=100%" style="table-layout: fixed;">';
echo '<tr>';
for ($x1 = 0 ; $x1 < pg_num_rows($result1) ; $x1++){
	//★レコード情報取得
	$rows1 = pg_fetch_array($result1, NULL, PGSQL_ASSOC);
	if($yoko==5){
		echo "</tr><tr>";
		$yoko=0;
	}
	echo '<td valign="top">';
	$yoko++;
	echo '<table class="scrollTable" cellpadding="0" cellspacing="0" border="0"><thead>';
	echo '<tr><td colspan="3"><b>';
	echo '<a href="list.php?table='.$rows1["tablename"].'" target="_blank">'.$rows1["tablename"].'</a>';
	//.$rows1["tablename"].
	echo "</b></td></tr>";
	echo "</thead><tbody>";
	$sqlact="SELECT * FROM ".$rows1["tablename"];
	$result = pg_query($sqlact);
	$i = pg_num_fields($result);
	$column=pg_num_fields($result);
	$thlist="";
	$thlist1="";
	$thlist2="";	
	for ($j = 0; $j < $i; $j++) {
		$fieldname = pg_field_name($result, $j);
		if($fieldname==$rows1["tablename"]."_id"){//主キー
			$thlist=$thlist."<tr>";
			$thlist=$thlist."<td>主</td>";
			$thlist=$thlist."<td>".pg_field_name($result, $j)."</td>";
			$thlist=$thlist."<td>".pg_field_type($result, $j)."</td>";
			$thlist=$thlist."</tr>";
		}elseif(preg_match("/_id/", $fieldname)){//外部キー
			$thlist1=$thlist1."<tr>";
			$thlist1=$thlist1."<td>外</td>";
			$thlist1=$thlist1."<td>".pg_field_name($result, $j)."</td>";
			$thlist1=$thlist1."<td>".pg_field_type($result, $j)."</td>";
			$thlist1=$thlist1."</tr>";
		}else{//その他
			$thlist2=$thlist2."<tr>";
			$thlist2=$thlist2."<td></td>";
			$thlist2=$thlist2."<td>".pg_field_name($result, $j)."</td>";
			$thlist2=$thlist2."<td>".pg_field_type($result, $j)."</td>";
			$thlist2=$thlist2."</tr>";
		}
	}
	echo $thlist;
	echo $thlist1;
	echo $thlist2;
	echo "</tbody>";
	echo "</table>";
	echo "</td>";
/*

*/
	//echo ',<a href="list.php?table='.$rows1["tablename"].'" target="_blank">'.$rows1["tablename"].'</a>';
	//echo '<a href="csv.php?table='.$rows1["tablename"].'">'.$rows1["tablename"].'ダウンロード</a><br>';
	//★テーブル作成
}
//★★終了

echo "</tr></table>";


$close_flag = pg_close($link);
if ($close_flag){
//    print('切断に成功しました。<br>');
}
?>

<?php $features = "width=400, height=300, menubar=no, toolbar=no, scrollbars=yes"; ?>
</body>
</html>