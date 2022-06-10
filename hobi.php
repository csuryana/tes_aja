<?
/*tes*/
ob_start();
include("$DOCUMENT_ROOT/s/config.php");
$title_off=1;
$template->basicheader();
$debug  ="0";
$title	="";
$menu = array("warna_rambut.php"      => "Warna Rambut",
              "bentuk_hidung.php"     => "Bentuk Hidung",
              "bentuk_muka.php"       => "Bentuk Muka",
              "warna_kulit.php"       => "Warna Kulit",
              "golongan_darah.php"    => "Gol Darah",
              "hobi.php"              => "Hobi",
              "daya_tahan.php"        => "Daya Tahan",
              "bakat.php"             => "Bakat/Talenta");
$template->tab_top_relative($title,$menu); 

/*=========================================
 PAGE CONFIG
=========================================*/
$menu 		        = array(basename($PHP_SELF)=>"");
$standar_button_off	="0";
$navigation_off		="1";
$title_off			="1";
$cfg_reff_table		="SPG_HOBBY";
$title				= "Hobi";
$cfg_header 		= array("Nama Hobi");
$cfg_data			= array("nm_hobby");
$cfg_title			= "Hobi";
$cfg_subtitle		= "";
$primary_key		= "id";
$attr_key1			= "id";
$prefix_primary_key = "";
$_primary_key		=$f->primary_key($primary_key);
if(!$start) 		$start='0';
if(!$order)			$order='kd_hobby';
if(!$sort) 			$sort='asc';	
if(!$page) 			$page='0';
if(!$num)			$num='10';
$start=($page-1)*$num;
if($start < 0) $start='0';

echo"
<center>
";
//$template->tab_top($title,$menu);
if($act=='delete'){
#	$f->checkaccessdelete("$access_level_privillege");
 	$_old_val=$f->convert_value(array("table"=>"$cfg_reff_table","cs"=>"$attr_key1","cd"=>"$primary_key","vd"=>$$primary_key,"print_query"=>1));
 	$sql="delete from $cfg_reff_table where $_primary_key";
 	echo"$sql<hr>";
 	$db->Execute("$sql");
	//$f->insert_log("DELETE $kurs, $primary_key: ".$$primary_key."");
	header("Location:$HTTP_REFERER");
	ob_end_flush();
	exit;
	
}elseif($act=="do_add"||$act=="do_update"){
#	$f->checkaccesswrite("$access_level_privillege");
	foreach ( $_POST as $key=>$val) {
		$key=strtolower($key);
		$$key=htmlspecialchars($val,ENT_QUOTES);
		$HTTP_POST_VARS[$key]=htmlspecialchars($val,ENT_QUOTES);
		$_POST[$key]=htmlspecialchars($val,ENT_QUOTES);
	}
	$sql="select $primary_key from $cfg_reff_table where nm_hobby='$nm_hobby'";
			if($f->check_exist_value($sql)==true) $f->box("Invalid Input","<P>Data sudah terdapat dalam database!","","error","");
	if (empty ($nm_hobby))  $error.="<li> Hobi harus diisi !";
	if($act == 'do_add')
	{
	
	$max_kd_hobby =$f->count_total("$cfg_reff_table","");
	}
	// Kondisi ini ada karena field kd_hobby di table spg_hobby di set char(1).
		if($max_kd_hobby == 9) 
			$error .="<li>Jumlah data hanya maksimal 9";
    
    if($error){
    $_error ="<B>ERROR: </B>
    <ul>$error</ul><P>
    <B>&laquo;</B> <a href=javascript:; onClick=javascript:history.back(-1)>Kembali</a>";

    $f->box("Invalid Input","$error","","error","");

	}else{
		/*===================================================================
		CHECK KONDISI
		====================================================================*/
		$_c_primary_key=preg_replace("#,#","|",$primary_key);
		
		if($act=='do_add'){
		/*===================================================================
        MENAMBAH ID 
        ====================================================================*/
        $max_id=$f->max_id(array("table"=>"$cfg_reff_table"));
        if(empty($max_id)){
        $max_id=1;
        }else{
        $max_id=$max_id+1;
        }

        $key_id =$f->generate_nomorkolom("$cfg_reff_table","$primary_key","$prefix_primary_key");
        $columns ="$primary_key,kd_hobby,";
        $values ="'$max_id','$max_id',"; # insert ke kolom id		
		}
#		die("x: $primary_key");
		
		foreach($HTTP_POST_VARS as $key=>$val){

			if($act=='do_add' && !preg_match("/^(act|$primary_key)$/i",$key)){
				
				$columns .="$key,";
				if(eregi("tgl|tanggal",$key)){
					$values .="to_date('$val','dd/mm/yyyy'),";	
				}else{
					$values .="'$val',";
				}				

			}elseif($act=='do_update' && !preg_match("/^(act|$_c_primary_key)$/i",$key)){
				if(eregi("tgl|tanggal",$key)){
					$list .="$key=to_date('$val','dd/mm/yyyy'),";	
				}else{
					$list .="$key='$val',";					
				}
			}

		}
		$columns = preg_replace("/,$/","",$columns);
		$values	 = preg_replace("/,$/","",$values);
		$list	 = preg_replace("/,$/","",$list);
		$cond_primary_key = $f->primary_key($primary_key);
		
		echo"<P>";

		if($act=="do_update"){ 
			if(empty($primary_key))  $f->box("Invalid Input","<P>Nama $primary_key","","error","");
			$sql="update $cfg_reff_table set $list where $cond_primary_key";
			$result=$db->Execute("$sql");
			if (!$result){
				print $db->ErrorMsg();
				die($sql);
			}
			$f->insert_log("UPDATE $title. $primary_key: ".($$primary_key),"$sql");

			$f->result_message("Data telah di update<P>
			<a href=$PHP_SELF>&larr; Kembali</a>");
		}else{

			$sql_insert	="insert into $cfg_reff_table  ($columns) values ($values)";
			$result=$db->Execute($sql_insert);
			if (!$result){
				print $db->ErrorMsg();
				die($sql_insert);
			}	

			$f->result_message("Data telah direkam
			<P><a href=$PHP_SELF>&larr; Kembali</a>");
			$f->insert_log("INSERT $title. $primary_key ".($$primary_key));
		}
	}

}elseif($act=="add"||$act=="update"){

	$_act=($act=='update')?"do_update":"do_add";
	$template->subtitle("$cfg_subtitle");
	
	if($act=='update'){
		$sql="select * from $cfg_reff_table where ".$_primary_key;
		$result_array=$f->get_last_record($sql);
		foreach($result_array as $key=>$val) $$key=$val;

	}
	echo"
	<table class=index>
	<form method=post name=f1>
	<input type=hidden name=act value=$_act>
	<input type=hidden name='$primary_key' value='".$$primary_key."'>
	<tr>
		<th colspan=2>Tambah/Ubah $title</th>
	</tr>
	<tr>
		<td>Hobi *</td>
		<td><input type=text name=nm_hobby value='$nm_hobby' size=20>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td><td valign=top>
		<input type=button onClick=history.back(-1); value='&larr; Kembali'>
		<input type=submit value='Simpan &rarr;' class=buttonhi>
		</td>
	</tr>
	</table>
	</form>
	";		

}else{
	if(!empty($query)){
			$query	= urldecode($query);
			$query	= strtolower(trim($query));
			$query =  htmlspecialchars($query,ENT_QUOTES);
			$rel 	= !empty($cond)?"and":"where";
			$cond  .=" $rel (lower(kd_hobby||nm_hobby) like '%$query%')";
	}	
	$total = $f->count_total("$cfg_reff_table","$cond");

	$f->standard_buttons();	
	$f->search_box($query);
	$f->paging(array("link"=>$PHP_SELF."?order=$order&sort=$sort&type=$type&act=","page"=>$page,"total"=>$total,"num"=>"10","show_total"=>1));
	$sql="select * from $cfg_reff_table $cond order by $order $sort";
	$result=$db->SelectLimit("$sql","$num","$start");
	if(!$result) print $db->ErrorMsg();
	$_sort=($sort=='desc')?"asc":"desc";
	echo"
	<table class=index>
	<tr>

		<th width=5><B>No</th>";
		foreach($cfg_header as $val){
			echo"<th>".strtoupper($val)."</th>";	
		}
		echo"
		<th>Fungsi</th>
	</tr>
	";
	while($val=$result->FetchRow()){
		$i++;
		$bgcolor= ($i%2)?"#FFDDDD":"FFFFFF";
		foreach($val as $key1 => $val1){
			$key1=strtolower($key1);
			$$key1=$val1;
		}
		echo"
		<tr bgcolor=$bgcolor>
			<td valign=top align=center>".($i+$start)."</td>";
			
			foreach($cfg_data as $_cfg_data){
				$value=$$_cfg_data;
				if($_cfg_data=='nilkurs'){
					$align=" align=right";
					$value=number_format($value,"",",",".");
				}
				echo"<td valign=top $align>$value</td>";	
				unset($align);
			}
			echo"
			<td  valign=top width=200>
				<a href=$PHP_SELF?act=update&$primary_key=".$$primary_key."><img src=/images/button_edit.gif border=0></a>
				<a href=$PHP_SELF?act=delete&$primary_key=".$$primary_key." onClick=\"javascript:return confirm('Anda Yakin Menghapus Data ini?');return false;\"><img src=/images/button_delete.gif border=0></a>
			</td>
		</tr>
		";
		
		unset($_status,$tp);
	}
	if($i==0){
	echo"<tr>
			<td colspan=8 align=center>** TIDAK ADA DATA **</td>
		</tr>";
	}
	echo"
	</table>";

	$f->paging(array("link"=>$PHP_SELF."?order=$order&sort=$sort&type=$type&act=","page"=>$page,"total"=>$total,"num"=>"10","show_total"=>1));


}
$template->tab_bottom();
?>
