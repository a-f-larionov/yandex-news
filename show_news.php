<style>
	td{
		border-bottom:1px dotted black;
	}
	.author-li{
		list-style: none; /*убираем маркеры списка*/

		border-radius: 4px;
		background:green;
		color:white;
		width:100px;
		text-align:center;
		cursor:hand;
		display:inline;
		padding:4px 10px;
	}

	.author-li:hover{
		background:lightgreen;
	}
</style>

<?php

if(!isset($_GET['mode'])) $_GET['mode'] = 'nomode';


if($_GET['mode']=='setAuthor'){
?>

<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script>

$(document).ready(function(){

		console.log('mode : set author');

		$('.js-set-author').on('click', function(){
			var authorName = $(this).data('author');
			var newsId = $(this).data('news_id');
			var isItOk = confirm('Уверены что автор статьи:' + authorName);
			var self = this;

			if(isItOk){

				$.get('/alor_set_author.php?authorName=' + authorName + '&newsId='+ newsId + '&encode=utf-8', {}, function(){
					console.log(newsId, authorName);
					$(self).parent().html('Сохраненно:' + authorName);
				});


		}
		console.log(isItOk);
		});

});
</script>
<?php
}
?>

<?php
	$serverName = '127.0.0.1';
	$userName = 'alor';
	$password = 'alor';
	$dbName = 'alor';

	$conn = new mysqli($serverName, $userName, $password, $dbName);

	$conn->query('SET NAMES utf8');
	$conn->query('SET CHARSET UTF8');

if(isset($_GET['month'])){
	$month = (int)$_GET['month'];


	$timeLeft = strtotime("21.".$month.".2019");
	$timeRight = strtotime("22.".($month+1).".2019");



	$result = $conn->query('SELECT * FROM news WHERE time >= '.(int)$timeLeft.' AND time <= '.(int)$timeRight.' ORDER BY time ASC');
}else{

	$result = $conn->query('SELECT * FROM news ORDER BY time ASC');
}


$count =0;
$count_empty = 0;
	echo "<table>";

	$authorGroups = [];

	while($row = $result->fetch_assoc()){
		if(!isset($authorGroups[$row['author']])) $authorGroups[$row['author']] = 0;
		$authorGroups[$row['author']]++;

		echo "<tr>";

		echo "<td>" . $row['title']. "</td>";

		$date = date('d.m.Y' , $row['time']);

		echo "<td>".$date. "</td>";

		echo "<td>" . $row['sourceName']. "</td>";

		echo "<td style='width:5%;'><a href='" . $row['url']. "'>".$row['url']."</a></td>";

		$author = getAuthor($row);


		if(empty($row['author'])) $count_empty ++;
		echo "<td style='width:90%;'>$author</td>";

		echo "</tr>";
		$count++;
	}


	echo "<tr>";
	echo "<td>Не проставлено: $count_empty</td>";
	echo "<td>Всего:$count</td>";
	echo "<td>$count</td>";
	echo "<td>$count</td>";
	echo "<td>$count</td>";
	echo "</tr>";

	echo "</table>";

	if($_GET['mode']=='setAuthor'){?>

		<script>
			window.authorGroups = <?=json_encode($authorGroups, false);?>;
		</script>
		<?php
	}



function getAuthor($row){
	$words = [
		'Алор' => 'Алор',
		'Яковенко' => 'Яковенко',
		'Конюхова' => 'Конюхова',
		'Антонов' => 'Антонов',
		'Корюхин' => 'Кор[ю|Ю]хин' ,
		'Дрёмин' => 'Др[е|ё]мин',
		'Рапотьков' => 'Рапотьков',
		'Веревкин' => 'Веревкин',
		'Мустяца' => 'Мустяц',
		'Кухтенков' => 'Кухтенков',//Дмитрий Кухтенков
		'>Не наше<' => 'Дазар\'алор',
	];

	$buttons = '<ul>';
	foreach($words as $title => $pattern){

		$buttons .= '<li class="js-set-author author-li" data-author="'.$title.'" data-news_id="'.$row['id'].'"  ';
		$buttons.= ' style="font-size:14px;">';
		$buttons .= ' ' . $title . '</li> ';
	}

	$buttons .= "</ul>";

	$content = $row['content'];

	if($row['author']) {
		return $row['author'];
	}

	if(strlen($row['content'])==0) return $buttons. ' no loaded';


	if($content=='SKIPPED')return $buttons . '  '. $content;

$out = '';
// тут да... не знаю как еще рег эксп сделать)
	$content = strip_tags($content);
	$content = html_entity_decode ($content);
	$content = str_replace("&nbsp;", " ", $content);
	$content = str_pad($content,200," ",STR_PAD_LEFT);



	$out .= $buttons;
	foreach($words as $title => $pattern){



		preg_match_all("#.*(.{45}$pattern.{0,45}).*#iu", $content, $matches);
		if(!count($matches[1]))continue;

		$out .= "<br>";
		$out .= str_ireplace($pattern, "<b>$pattern</b>",implode('<br>', $matches[1]));
		$out .= "<br>";
	}

	return $out;
}



