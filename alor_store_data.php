<?

	$time =(int) $_GET['time'];
	$url = $_GET['url'];
	$title = ( $_GET['title']);
	$smiName = ($_GET['smiName']);
	$author = $_GET['author'];
	$encode = $_GET['encode'];

	if($encode == 'windows-1251'){
		$title =  mb_convert_encoding($title, "utf-8", "windows-1251");
		$smiName = mb_convert_encoding($smiName, "utf-8", "windows-1251");
		$author = mb_convert_encoding($author, "utf-8", "windows-1251");
	}

    $serverName = '127.0.0.1';
    $userName = 'alor';
    $password = 'alor';
    $dbName = 'alor';

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	$conn = new mysqli($serverName, $userName, $password, $dbName);


	$conn->query("SET NAMES UTF8");

	$conn->query("SET CHARSET UTF8");


	if($conn->connect_error){
		die('Connection failed:'+$conn->connect_error);
	}

	if(!$title || !$time || !$url || !$smiName || !in_array($encode, ['utf-8','windows-1251'])){
		echo 'time:' .$time . '<br> title: ' . $title . '<br> smiName: ' . $smiName . '<br> url: ' . $url . '<br>author: ' . $author ;
		die("<b style='color:red;'>ERROR</b>");
	}
	
	
	

	
	$result = $conn->query("SELECT * FROM news WHERE title='$title' AND time=$time AND url='$url' AND sourceName='$smiName'");
	
	$notUnique = $result->fetch_assoc();
	if(!$notUnique){
		$result = $conn->query("SELECT * FROM news WHERE url='$url' ");

		$notUnique = $result->fetch_assoc();
		if($notUnique){
			
			// update date heere!
			
			echo 'url duplicate';
			
			$stmt =	$conn->prepare("UPDATE news SET `time` = ? WHERE `url` = ? ");
			$stmt->bind_param("is", $time, $url);
			$result  = $stmt->execute();
		}
	}
	
	
	if($notUnique){

		echo "time formated=" . date('Y-m-d H:i:s', $time) . '<br>';
		echo "<b style='color:red;'>DUBLICATE</b>";
		echo "
			<script>

				setTimeout(window.close, " .( $_GET['timeout'] ?? 1000) . ");
			</script>
	";
		die;
	}


	$stmt =	$conn->prepare("INSERT INTO news(`title`, `time`, `url`, `sourceName`, `author`) VALUES(?, ?, ?, ?, ?) ");

//	$title = 'Текст';

	$stmt->bind_param("sisss", $title, $time, $url, $smiName, $author);

	$result  = $stmt->execute();

	var_dump($result);
	var_dump($result);
	var_dump($result);

	
	//$result = $conn->query("INSERT INTO news( title, time, url, sourceName, author) VALUES ('$title', '$time', '$url', '$smiName', '$author') ");

	if(!$result){
			
	}

	$conn->close();



echo "title=" . $title . '<br>';
echo "smiName=" . $smiName . '<br>';
echo "url=" . $url . '<br>';
echo "author=" . $author . '<br>';
echo "encode=" . $encode . '<br>';
echo "time=" . $time . '<br>';

echo "time formated=" . date('Y-m-d H:i:s', $time) . '<br>';

echo "OK";

echo "
	<script>
		setTimeout(window.close, " . ($_GET['timeout'] ?? 5000) . ");
	</script>
";
