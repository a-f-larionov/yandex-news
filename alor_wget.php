<?php


echo "______________________________________<br>";
echo "\r\n<br>START\r\n";
echo "<br>TIME:" .date('Y-m-d H:i:s');
echo "<br>";

    $serverName = '127.0.0.1';
    $userName = 'alor';
    $password = 'alor';
    $dbName = 'alor';

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	$conn->query("SET NAMES UTF8");
	$conn->query("SET CHARSET UTF8");


	$result =	$conn->query("SELECT * FROM news WHERE content IS NULL AND (author IS NULL OR author = '')ORDER BY time desc");

	$row = $result->fetch_assoc();

		
	echo ($row['url']);
    echo "<br>";
    echo date('Y-m-d H:i:s', $row['time']);
    
	try {
		$content = file_get_contents($row['url']);
	}
	catch(Exception $e){
		$content = "EXCEPTION";
	}

echo "\r\n<br>id:".$row['id'];
echo "\r\n<br>url:".$row['url'];
echo "\r\n<br>len:". strlen($content);

if(strpos($content, 'windows-1251')){
	echo "\r\n<br> convert to utf8 from windwos 1251";
    $content = mb_convert_encoding($content, "utf-8", "windows-1251");
}

	$content = strip_tags($content);
	$content = html_entity_decode ($content);
	$content = str_replace("&nbsp;", " ", $content);
	$content = trim($content);

echo "\r\n<br>len:". strlen($content);


	$id =(int) $row['id'];
	
	$stmt = $conn->prepare("UPDATE news SET content= ? WHERE id =? ");
	$stmt->bind_param('si', $content, $id );
	$stmt->execute();


	$conn->query("UPDATE news SET content='SKIPPED' WHERE content IS NULL AND id=".(int)$id);


	$conn->close();


	
echo "\r\n<br>OK";

