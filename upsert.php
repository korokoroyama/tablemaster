<?php
//★★引数継承
if(empty($_POST["table"])){
}else{
	$table=$_POST["table"];
}
if(empty($_POST["id"])){
}else{
	$id=$_POST["id"];
}
if(empty($_POST["flag"])){
}else{
	$flag=$_POST["flag"];
}
//★★htmlヘッダ作成
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<title>'.$table."確認ページ:".date("y/m/d")."-".date("H:i").'</title></head>';
?>

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
//★★継承
//require 'php/class.php';

//★★DB接続
//共通ファイルの読み込み
require_once('./common.php');
 
//コネクション取得
$conn = getConnection();  //←共通ファイルのfunctionが使える


//★★SQL作成
$sqlact="select * ";
$sqlact=$sqlact."FROM ".$table;
$sqlact=$sqlact." WHERE ".$table."_id = ".$id;

$key=$table."_id";
//★★SQLリスト
echo $sqlact;
echo "<hr>";
//★★SQL実行
$result = pg_query($sqlact);

$updatesql="UPDATE ".$table." SET ";
$insertsql="INSERT INTO ".$table." values (";

echo '<table rules="all">';
echo '<tr>';
echo '<th>カラム</th>';
echo '<th>値</th>';
echo '<th>型</th>';
echo '<th>変更後</th>';
echo '</tr>';

$tempdot=0;
$cut = 1;//カットしたい文字数
$i = pg_num_fields($result);
$column=pg_num_fields($result);
$rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
echo '<form action="upsert.php" method="POST">';
for ($j = 0; $j < $i; $j++) {
	$fieldname = pg_field_name($result, $j);
	if($j==0){
	}else{
		$updatesql=$updatesql.",";
		$insertsql=$insertsql.",";
	}
	if($tempdot==1){
		$tempdot=0;
		$updatesql = substr( $updatesql , 0 , strlen($updatesql)-$cut );
	}
	if($fieldname==$key){//主キー
		echo "<tr>";
		echo "<td>主キー:".$fieldname."</td>";
		echo "<td>".$rows[$fieldname]."</td>";
		echo "<td>".pg_field_type($result, $j)."</td>";
		echo '<td>'.$rows[$fieldname]."</td>";
		echo '<input type="hidden" name="'.$fieldname.'" value="'.$rows[$fieldname].'"/>';
		echo "</tr>";
		$insertsql=$insertsql.$id;
		if($j==0){
			$tempdot=1;
		}else{
			$updatesql = substr( $updatesql , 0 , strlen($updatesql)-$cut );
		}
	}elseif(preg_match("/_id/", $fieldname)){//外部キー
		$tablelist1=$tablelist1."<tr>";
		$tablelist1=$tablelist1."<td>外部キー:".$fieldname."</td>";
		$tablelist1=$tablelist1."<td>".$rows[$fieldname]."</td>";
		$tablelist1=$tablelist1."<td>".pg_field_type($result, $j)."</td>";
		$tablelist1=$tablelist1.'<td><select name="'.$fieldname.'">';
		$sqlact1="select * from ".substr($fieldname,0,-3);
		$result1 = pg_query($sqlact1);
		for ($x1 = 0 ; $x1 < pg_num_rows($result1) ; $x1++){
			//★レコード情報取得
			$rows1 = pg_fetch_array($result1, NULL, PGSQL_ASSOC);
			//$array[substr($fieldname,0,-3)][$rows1[substr($fieldname,0,-3)."_id"]]=$rows1[substr($fieldname,0,-3)."_name"];
			$tablelist1=$tablelist1.'<option value="'.$rows1[substr($fieldname,0,-3)."_id"].'"';
			if((int)$rows[$fieldname]===(int)$rows1[substr($fieldname,0,-3)."_id"]){
				$tablelist1=$tablelist1." selected";
			}
			$tablelist1=$tablelist1.'>';
			//echo $rows[$fieldname].":".$rows1[substr($fieldname,0,-3)."_id"].":";
			$tablelist1=$tablelist1.$rows1[substr($fieldname,0,-3)."_name"].'</option>';
		}
		$tablelist1=$tablelist1."</select>";
		$tablelist1=$tablelist1."</td>";
		$tablelist1=$tablelist1."</tr>";
		if(!empty($_POST[$fieldname])){
			$updatesql=$updatesql.$fieldname." = ".$_POST[$fieldname];
		}else{
			$updatesql = substr( $updatesql , 0 , strlen($updatesql)-$cut );
		}
		$insertsql=$insertsql.$_POST[$fieldname];
	}else{//その他
		$tablelist=$tablelist."<tr>";
		$tablelist=$tablelist."<td>".$fieldname."</td>";
		$tablelist=$tablelist."<td>".$rows[$fieldname]."</td>";
		$tablelist=$tablelist."<td>".pg_field_type($result, $j)."</td>";
		$tablelist=$tablelist.'<td><input type="text" name="'.$fieldname.'" value="'.$rows[$fieldname].'"/>'."</td>";
		$tablelist=$tablelist."</tr>";
/*		echo "<tr>";
		echo "<td>".$fieldname."</td>";
		echo "<td>".$rows[$fieldname]."</td>";
		echo "<td>".pg_field_type($result, $j)."</td>";
		echo '<td><input type="text" name="'.$fieldname.'" value="'.$rows[$fieldname].'"/>'."</td>";
		echo "</tr>";*/
		if(pg_field_type($result, $j)=="text"){
			if(!empty($_POST[$fieldname])){
				$updatesql=$updatesql.$fieldname." = '".$_POST[$fieldname]."'";
			}else{
				$updatesql = substr( $updatesql , 0 , strlen($updatesql)-$cut );
			}
			$insertsql=$insertsql."'".$_POST[$fieldname]."'";
		}else{
			if(!empty($_POST[$fieldname])){
				$updatesql=$updatesql.$fieldname." = ".$_POST[$fieldname];
			}else{
				$updatesql = substr( $updatesql , 0 , strlen($updatesql)-$cut );
			}
			$insertsql=$insertsql.$_POST[$fieldname];
		}
	}
}
echo $tablelist1;
echo $tablelist;
echo "</table>";

echo '<input type="hidden" name="table" value="'.$table.'"/>';
echo '<input type="hidden" name="id" value="'.$id.'"/>';

//echo '<input type="button" value="更新する">';

if($_POST['flag']=='追加準備'){
	echo '<input type="hidden" name="flag" value="追加"/>';
	echo '<input type="submit" name="submit" value="追加する" />';
}else{
	echo '<input type="hidden" name="flag" value="更新"/>';
	echo '<input type="submit" name="submit" value="更新する" />';
}

echo '</form> ';


$updatesql=$updatesql." WHERE ".$table."_id = ".$id;
$insertsql=$insertsql.")";


echo '<hr>';
if($_POST['flag']=='更新'){
	echo $updatesql;
	$result = pg_query($updatesql);
	if (!$result) {
	              die('<hr>失敗です。'.pg_last_error());
	}else{
		echo '<hr>更新しました';
	}
}
if($_POST['flag']=='追加'){
	echo $insertsql;
	$result = pg_query($insertsql);
	if (!$result) {
	              die('<hr>失敗です。'.pg_last_error());
	}else{
		echo '<hr>追加しました';
	}
}

//★★終了

$close_flag = pg_close($link);
if ($close_flag){
//    print('切断に成功しました。<br>');
}
?>

</body>
</html>