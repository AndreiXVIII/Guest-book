<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Test </title>
		<style>
		/*Так как в этом минипроекте один php файл, css решил не выносить в отдельный файл */
			#wrapper {
				width: 500px;
				margin: 0px auto;
				padding: 10px;
				text-align: justify;
			}
			.users {
				
				margin-top:30px;
				padding: 0px, 10px;
			}
			.date {
				color: blue;
				font-weight: bold;
			}
			.name {
				color: blue;
				font-size: 12pt;
			}
			#result {
				width: 100%;
				background: #5EC55E;
			}
			.control {
				width: 100%;
			}
			.controlTextarea {
				width: 100%;
				min-height: 150px;
				resize: vertical;
				text-align: justify;
			}
			a {
				text-decoration: none;
			}
			a.active {
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		<div id="wrapper">
			<h1> Гостевая книга </h1>
			<?php
				$local = 'localhost';
				$user = 'root';
				$password = '';
				$db_name = 'test';
				
				$connect = mysqli_connect($local, $user, $password, $db_name) or die (mysqli_error($connect));
				mysqli_query($connect, "SET NAMES 'utf8'");		
				
				//Добавляем запись в гостевую книгу, при условии того, что поля заполнены
				if (!empty($_REQUEST['name']) && !empty($_REQUEST['message'])) {
					$name = $_REQUEST['name'];
					$message = $_REQUEST['message'];
					$dates = date('Y-m-d H:i:s');
							
					$query = "INSERT INTO guestBook SET name='$name', message='$message', id_dates='$dates'";
					mysqli_query($connect, $query) or die (mysqli_error($connect));
				}				
				
				//Указываем количество записей, которые хотим вывести: с какой и по какую страницу, заданные через LIMIT
				if (isset($_GET['page'])) {
					$page = $_GET['page'];
				} else {
					$page = 1;
				}
				
				$records = 3;
				$entriesOnThePage = ($page - 1) * $records;
				
				$query = "SELECT * FROM guestBook ORDER BY id_dates DESC LIMIT $entriesOnThePage, $records";
				$result = mysqli_query($connect, $query) or die (mysqli_error($connect));
				for ($array = []; $row = mysqli_fetch_assoc($result); $array[] = $row);
				
				//Получаем колличество записей в гостевой книге
				$query = "SELECT COUNT(*) as count FROM guestBook";
				$result = mysqli_query($connect, $query) or die (mysqli_error($connect));
				$count = mysqli_fetch_assoc($result)['count'];
				
				//Узнаем какое количество страниц должно быть для вывода записей 	
				$pagesCount = ceil($count / $records);
				
				//Полученое количество страниц через цикл выводим ссылками. Для активной ссылки задаем клаас active - для подсветки
				//Также делаем стрелочку на предыдущую страницу, и на следующую
				if ($page != 1) {
					$prev = $page - 1;
					echo "<a href=\"?page=$prev\"> << </a> ";
				}
				else {
					echo "<< ";
				}
				for ($i = 1; $i <= $pagesCount; $i++) {
					if ($page == $i) {
						$class = ' class="active"';
					}
					else {
						$class = '';
					}
					echo " <a href=\"?page=$i\"$class> $i </a> ";
				}
				if ($page != $pagesCount) {
					$next = $page + 1;
					echo " <a href=\"?page=$next\"> >> </a>";
				}
				else {
					echo " >>";
				}
				
				//Выводим отзывы гостей на экран
				foreach ($array as $guest) {
					echo "<div class=\"users\">
							<p><span class=\"date\"> {$guest['id_dates']} </span>
							   <span class=\"name\"> {$guest['name']} </span>
							</p>
							<p> {$guest['message']} </p>
					</div>";
				}
				
				//Функция уведомляет об успешнй записи
				function check($name, $message) {	
					if (isset($name) && isset($message)) {
						return $test = "<p> Запись успешно сохранена </p>";
					}
					else {
						return $test = "<p> </p>";
					}
				}
			?>
			<div id="result">
				<p> <?=  check($name, $message) ?> </p>
			</div>
			<div id="form">
				<form method="POST">
					<input name="name" class="control" placeholder="Ваше имя"><br><br>
					<textarea name="message" class="controlTextarea" placeholder="Ваш отзыв"></textarea><br><br>
					<input type="submit" class="control">
				</form>
			</div>
		</div>	
	</body>
</html>