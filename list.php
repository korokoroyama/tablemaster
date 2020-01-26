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

//★★SQL作成
$sql[0]="select * ";
$sql[0]=$sql[0]."FROM ".$table;
if(!empty($sequence)){
              $sql[0]=$sql[0]." order by ".$sequence;
}else{
	if(0 === strncmp($table, 'm_', 2)){
		$sql[0]=$sql[0]." order by ".$table."_id";
	}
}

//★★SQLリスト
for ($i=0 ; $i < count($sql) ;$i++){
	if($i==$sql_number){
		$sqlact=$sql[$i];
		echo "★";
	}
	echo '<a href="list.php?sql_number='.$sql_number.'" target="_blank">'.$sql[$i]."</a><br>";
}
echo "<hr>";

//★★SQL実行
$result = pg_query($sqlact);
echo '<table class="scrollTable" cellpadding="0" cellspacing="0" border="0"><thead>';
echo '<tr>';
echo "<th>番号</th>";
$i = pg_num_fields($result);
$column=pg_num_fields($result);
for ($j = 0; $j < $i; $j++) {
	$fieldname = pg_field_name($result, $j);
	$countries[$j] = $fieldname;
	if($fieldname==$table."_id"){//主キー
		$id=$j;
		echo "<th>";
		echo '<a href="list.php?table='.$table."&sequence=".$fieldname.'" target="_blank">★主キー</a>';
//		echo $fieldname;
//		echo "<br>".pg_field_type($result, $j);
		echo "<br>ID";
		echo "</th>";
	}elseif(preg_match("/_id/", $fieldname)){//外部キー
		$thlist=$thlist."<th>";
		$thlist=$thlist.'<a href="list.php?table='.$table."&sequence=".$fieldname.'" target="_blank">◆外部キー</a>';
		$thlist=$thlist."<br>".$fieldname;
		$thlist=$thlist."<br>型:".pg_field_type($result, $j);
		$sqlact1="select * from ".substr($fieldname,0,-3);
		$result1 = pg_query($sqlact1);
		for ($x1 = 0 ; $x1 < pg_num_rows($result1) ; $x1++){
			//★レコード情報取得
			$rows1 = pg_fetch_array($result1, NULL, PGSQL_ASSOC);
			$array[substr($fieldname,0,-3)][$rows1[substr($fieldname,0,-3)."_id"]]=$rows1[substr($fieldname,0,-3)."_name"];
		}
		$thlist=$thlist."</th>";
	}else{//その他
		$thlist=$thlist."<th>";
		$thlist=$thlist.'<a href="list.php?table='.$table."&sequence=".$fieldname.'" target="_blank">〇</a>'.$fieldname;
		$thlist=$thlist."<br>型：".pg_field_type($result, $j)."</th>";
	}
}
echo $thlist;

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
	for ($k = 0; $k < $column; $k++) {
		if( (0 === strncmp($table, 'm_', 2)) AND ($k==$id)){//主キー
			echo "<td>";
			echo '<script language="javascript"> ';
			echo 'function test'.$rows[$countries[$k]].'() { ';
			echo 'window.open("about:blank","ATMARK","width=600,height=450,scrollbars=yes");';
			echo 'window.document.inform'.$rows[$countries[$k]].'.action = "upsert.php" ;'; 
			echo 'window.document.inform'.$rows[$countries[$k]].'.target = "ATMARK" ; ';
			echo 'window.document.inform'.$rows[$countries[$k]].'.method = "POST" ; ';
			echo 'window.document.inform'.$rows[$countries[$k]].'.submit() ; ';
			echo '}';
			echo '</script>';

			echo '<form name="inform'.$rows[$countries[$k]].'">';
			echo '<input type="hidden" name="table" value="'.$table.'"/>';
			echo '<input type="hidden" name="id" value="'.$rows[$countries[$k]].'"/>';
			echo '<input type="button" value="'.$rows[$countries[$k]].'" onclick="test'.$rows[$countries[$k]].'();">';
			echo '</form> ';

			if($maxid <= $rows[$countries[$k]]){
				$maxid=$rows[$countries[$k]]+1;
			}
			echo "</td>";
		}elseif(preg_match("/_id/", $countries[$k])){//外部キー
			//$trlist=$trlist."<td>".$rows[$countries[$k]]."</td>";
			$trlist=$trlist."<td>".$array[substr($countries[$k],0,-3)][$rows[$countries[$k]]]."</td>";
			//★テスト用$trlist=$trlist."<td>".substr($countries[$k],0,-3).":".$rows[$countries[$k]]."★".$array[substr($countries[$k],0,-3)][$rows[$countries[$k]]]."</td>";
		}else{
			$trlist=$trlist."<td>".$rows[$countries[$k]]."</td>";
		}
	}
	echo $trlist;
	$trlist="";
	echo "</tr>";
}
echo "</tr></tbody></table>";
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
	echo ',<a href="list.php?table='.$rows1["tablename"].'" target="_blank">'.$rows1["tablename"].'</a>';
	echo '<a href="csv.php?table='.$rows1["tablename"].'">'.$rows1["tablename"].'ダウンロード</a><br>';
	//★テーブル作成
}
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