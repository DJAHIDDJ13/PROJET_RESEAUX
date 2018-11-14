<?php
	include_once('includes.php');
	session_start();

	function search_users($db, $username, $start_signup_date, $end_signup_date) {
		pg_prepare($db, "db_user_search", "SELECT * FROM Users Where (STRPOS(Username, $1)>0 OR $1='') AND (Signup_Date>=$2 OR $2 IS NULL) AND (Signup_Date<=$3 OR $3 IS NULL)");
		$start_signup_date = ($start_signup_date=='')?null:$start_signup_date;
		$end_signup_date = ($end_signup_date=='')?null:$end_signup_date;
		$result = pg_execute($db, "db_user_search", array($username, $start_signup_date, $end_signup_date));
		return $result;
	}
	function ban_user($uname) {
		// do stuff
	}
	function show_users($result) {
		if(pg_num_rows($result) == 0) {
			echo '<p>Aucune resultat trouvé</p>';
		} else {
			$i = 0;
			echo '<div class="user_table">';
			echo '<div class="user_table_row">';
			while ($i < pg_num_fields($result)) {
				$fieldName = pg_field_name($result, $i);
				echo '<div class="user_table_head">' . $fieldName . '</div>';
				$i = $i + 1;
			}
			echo '</div>';
			$i = 0;
			
			while ($row = pg_fetch_row($result)) {

				echo '<div class="user_table_row">';
				$count = count($row);
				$y = 0;
				$uname = current($row);
				while ($y < $count) {
					$c_row = current($row);
					echo '<div class="user_table_cell">' . $c_row . '</div>';
					next($row);
					$y = $y + 1;
				}
				$delete_date = current($row);
				echo '<div class="user_table_cell">';
					echo '<form action="" method="post" onsubmit="return confirm(\'Etes vous sur de bannir cet utilisateur?\', false)">';
						echo '<input type="hidden" name="ban_uname" value="'.$uname.'">';
						echo '<input type="submit" value="ban">';
					echo '</form>';
				echo '</div>';
				echo '</div>';
				$i = $i + 1;
			}
			echo '</div>';
		}
		pg_free_result($result);
	}
?>
<!DOCTYPE html>
<html>
	<header>
		<title>User control center</title>
		<style>
			.user_table    { display: table; }
			.user_table_row       { display: table-row; }
			.user_table_head    { display: table-header-group; }
			.user_table_cell, .user_table_head  { display: table-cell; }
		</style>
	</header>
	<body>
		<form action="" method="post">
			Username: <input type="text" name="uname"><br>
			Start signup date: <input type="date" name="StartSingupDate"><br>
			End signup date: <input type="date" name="EndSignupDate">
			<input type="submit" value="Search" name="search">
		</form>	
		<br>
		<div >
			<?php
				if(isset($_POST["search"])) {
					$result = search_users($db, $_POST["uname"], $_POST["StartSingupDate"], $_POST["EndSignupDate"]);
					show_users($result);
				} else if(isset($_POST["ban_uname"])) {
					ban_user($_POST["ban_uname"]);
				}
			?>
		</div>
	</body>

</html>
