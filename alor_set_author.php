<?

$authorName = $_GET['authorName'];
$encode = $_GET['encode'];
$newsId = (int)$_GET['newsId'];

if ($encode == 'windows-1251') {
    $authorName = mb_convert_encoding($authorName, "utf-8", "windows-1251");
}


$serverName = '127.0.0.1';
$userName = 'alor';
$password = 'alor';
$dbName = 'alor';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($serverName, $userName, $password, $dbName);


$conn->query("SET NAMES UTF8");

$conn->query("SET CHARSET UTF8");


if ($conn->connect_error) {
    die('Connection failed:' + $conn->connect_error);
}

if (!$authorName || !$newsId || !in_array($encode, ['utf-8', 'windows-1251'])) {
    echo '<br> newsId: ' . $newsId . '<br> authorName: ' . $authorName;
    die("<b style='color:red;'>ERROR</b>");
}


$stmt = $conn->prepare("UPDATE  news SET author = ? WHERE id = ?");
$stmt->bind_param("si", $authorName, $newsId);
$result = $stmt->execute();

var_dump($result);


$conn->close();


echo "author=" . $authorName . '<br>';
echo "newsId=" . $newsId . '<br>';
echo "encode=" . $encode . '<br>';

echo 'OK';