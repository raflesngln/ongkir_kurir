
<?php

//Get Data Kabupaten
$curl = curl_init();	
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.rajaongkir.com/starter/city",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
	"key: a35d5dd55c656cd6e2ed577b66189745"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

echo "<label>Kota Asal</label><br>";
echo "<select name='asal' id='asal'>";
echo "<option>Pilih Kota Asal</option>";
	$data = json_decode($response, true);
	for ($i=0; $i < count($data['rajaongkir']['results']); $i++) { 
		echo "<option value='".$data['rajaongkir']['results'][$i]['city_id']."'>".$data['rajaongkir']['results'][$i]['city_name']."</option>";
	}
echo "</select><br><br><br>";
//Get Data Kabupaten


//-----------------------------------------------------------------------------

//Get Data Provinsi
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.rajaongkir.com/starter/province",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
	"key: a35d5dd55c656cd6e2ed577b66189745"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

echo "Provinsi Tujuan<br>";
echo "<select name='provinsi' id='provinsi'>";
echo "<option>Pilih Provinsi Tujuan</option>";
$data = json_decode($response, true);
for ($i=0; $i < count($data['rajaongkir']['results']); $i++) {
	echo "<option value='".$data['rajaongkir']['results'][$i]['province_id']."'>".$data['rajaongkir']['results'][$i]['province']."</option>";
}
echo "</select><br><br>";
//Get Data Provinsi

?>

<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>

	<label>Kabupaten Tujuan</label><br>
	<select id="kabupaten" name="kabupaten"></select><br><br>

	<label>Kurir</label><br>
	<select id="kurir" name="kurir">
		<option value="jne">JNE</option>
		<option value="tiki">TIKI</option>
		<option value="pos">POS INDONESIA</option>
	</select><br><br>

	<label>Berat (gram)</label><br>
	<input id="berat" type="text" name="berat" value="500" />
	<br><br>

	<input id="cek" type="submit" value="Cek"/>

	<div id="ongkir"></div>
    <div id="boxservice">
        <ul class="list">
        <!-- TIPE SERVICE -->
        </ul>   
    </div>

</body>
</html>


<script type="text/javascript">
$(document).ready(function(){
	$('#provinsi').change(function(){
		//Mengambil value dari option select provinsi kemudian parameternya dikirim menggunakan ajax 
		var prov = $('#provinsi').val();
		  $.ajax({
			type : 'GET',
			   url : 'http://localhost/ongkir_kurir/cek_kabupaten.php',
			data :  'prov_id=' + prov,
				success: function (data) {
				//jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
				$("#kabupaten").html(data);
			}
		  });
	});

	$("#cek").click(function(){
		//Mengambil value dari option select provinsi asal, kabupaten, kurir, berat kemudian parameternya dikirim menggunakan ajax 
		var asal =$('#asal').val();
		var kab =$('#kabupaten').val();
		var kurir =$('#kurir').val();
		var berat =$('#berat').val();

		  $.ajax({
			type : 'POST',
			   url : 'http://localhost/ongkir_kurir/cek_ongkir.php',
			dataType: "JSON",
			data :  {'kab_id' : kab, 'kurir' : kurir, 'asal' : asal, 'berat' : berat},
				success: function (data) {
				//jika data berhasil didapatkan, tampilkan ke dalam element div ongkir
					// $("#ongkir").text(data);
					obj = JSON.parse(data);
					var status=obj.rajaongkir.status;
					var origin=obj.rajaongkir.origin_details.province;
					var desti=obj.rajaongkir.destination_details.province;

					console.log('status '+status.code+' origin-desti '+ origin+'-'+desti);
					var hasil=obj.rajaongkir.results[0].costs[1].cost[0].value;
					console.log('Harga Reguler '+ hasil);

					// Parsing tipe service oke,reg or yes
					var tipe_service=obj.rajaongkir.results[0].costs;
					$("#boxservice ul").empty();
					for(var a=0; a < tipe_service.length;a++){
						var nm_service=tipe_service[a].service;
						var ket_service=tipe_service[a].description;
						var harga_service=tipe_service[a].cost[0].value;
						console.log('nm_service '+nm_service);
						console.log('ket_service '+ ket_service);
						console.log('harga_service '+ harga_service);
						$("#boxservice ul").append("<li><input name='service' type='radio' value='"+harga_service+"'> "+nm_service +" ( Rp "+harga_service+")</li>");
						
					}
					
					

			}
		  });
	});
});
</script>