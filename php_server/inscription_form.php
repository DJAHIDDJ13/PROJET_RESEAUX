<?php

	include_once('includes.php');
	$now = date("d-m-Y");
	$time = date("H:i:s");

	if(isset($_POST['register'])){

		extract($_POST);
		$valid  = true ;
		$mail = strtolower($mail);
		$username = strtolower($username);
		
		pg_prepare($db, "db_register", "SELECT * from users WHERE email=$1 OR username=$2");
		$result = pg_execute($db, "db_register", array($mail,$username));
		$res= pg_fetch_assoc($result);

		//Vérifier email
		if($res['email']){
			$valid= false;
			$error_mail = "Ce mail existe déja";
		}

		// Verfication login
		if($res['username']){
			$valid= false;
			$error_username = "Ce Login existe déja";
		}

		// comparaison des deux mots de pass enregistre
		if(strcmp($password,$c_password ) != 0){

			$valid= false;
			$error_pass= "Les deux Mots de pass sont pas identiques ";
		}

		// validation format Tel
		if(!preg_match('`[0-9]{10}`',$phone)){

			$valid= false;
			$error_phone= "Format Telephone invalide ";
		}
		//Traiter image 
		if($_FILES['picture']['name']){
			$image = $username.$_FILES['picture']['name'];
			$target = "img/users/".basename($image);
			if(!move_uploaded_file($_FILES['picture']['tmp_name'], $target)){
				$valid = false;
			}
		}else{
			$image = null;
		}
		

		if($valid){
			// si toutes les variables sont validées on insere le compte ainsi que l'utilisateur 
			$password = password_hash($password, PASSWORD_DEFAULT);
			pg_prepare($db, "db_insert_account", "INSERT INTO account (username,user_password,is_admin) VALUES ($1, $2 ,$3)");
			$inser_a = pg_execute($db, "db_insert_account", array($username, $password ,'false'));
			$_SESSION['flash']=" Votre Compte été créer " ;
	
			if($inser_a){
				pg_prepare($db, "db_insert_user", "INSERT INTO users (username,email,last_name,first_name,description,birthday_user,phone_number,place_of_birth,signup_date,modification_date,user_picture) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)");
				$inser_u = pg_execute($db, "db_insert_user", array($username,$mail,$lastname,$firstname,$description,$birthday,$phone,$city,$now,$now,$image));
				if($inser_u){
					$_SESSION['flash']=" Votre Compte été créer " ;
					$_POST = array();
				}else{
					$_SESSION['flash'] ="Erreur à la création du compte " ;
					pg_prepare($db, "db_delete_account", "DELETE  FROM account WHERE username=$1");
					pg_execute($db, "db_delete_account", array($username));
				}
			}else{
				$_SESSION['flash'] ="Erreur à la création du compte " ;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="Stylesheet" href="./style.css" type="text/css" />
	<link rel="Stylesheet" href="./style_I.css" type="text/css" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
	<title>Inscription</title>
</head>
    <body id="connexionAdmin">
			<div class="inscription" style="background-color: white">
				<?php 
			    if(isset($_SESSION['flash'])){ 
			    ?>
					<div id="alert" ><a class="close"></a>
						<?= $_SESSION['flash']; ?>
					</div>
				<?php
				    unset($_SESSION['flash']);
				}
				?>
			</div>
			<form  method="post" class="inscription" enctype="multipart/form-data" >
				<p class="title">Inscription</p>
				<table>
					<tr >
						<td>
							<div >
							<label >Login:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="username" placeholder="Login" value="<?php if (isset($_POST['username'])) echo ($username); ?>" required="required"/>
			                    <div><small ><?php if (isset($error_username)) echo $error_username; ?></small></div>
			                </div>
						</td>
						<td>
							<div >
							<label >Mail :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="mail" placeholder="Mail" value="<?php if (isset($_POST['mail'])) echo $mail; ?>" required="required"/>
			                    <div><small><?php if (isset($error_mail)) echo $error_mail; ?></small></div>
			                </div>
							
						</td>
					</tr>
					<tr >
						<td>
							<div >
							<label class="label_I">Mot de Pass:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="password" name="password" placeholder="Mot de Pass" value="<?php if (isset($_POST['password'])) echo $password; ?>" required="required"/>
			                    <div><small><?php if (isset($error_pass)) echo $error_pass; ?></small></div>
			                </div>
						</td>
						<td>
							<div >
							<label class="label_I" >Confirmer:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="password" name="c_password" placeholder="Confirmer Mot de Pass" value="<?php if (isset($_POST['c_password'])) echo $c_password; ?>" required="required"/>
			                    <div><small><?php if (isset($error_pass)) echo $error_pass; ?></small></div>
			                </div>
						</td>
					</tr>
					<tr >
						<td>
							<div >
							<label class="label_I">Nom:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="lastname" placeholder="Nom" value="<?php if (isset($_POST['lastname'])) echo $lastname; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label class="label_I">Prenom:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="firstname" placeholder="Prenom" value="<?php if (isset($_POST['firstname'])) echo $firstname; ?>" required="required"/>
			                </div>
						</td>
					</tr>
					<tr >
						<td>
							<div >
							<label class="label_I">Date De Naissance:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="date" name="birthday"  value="<?php if (isset($_POST['birthday'])) echo $birthday ; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label class="label_I" >Ville :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="city" placeholder="Ville" value="<?php if (isset($_POST['city'])) echo $city; ?>" />
			                    <div><small></small></div>
			                </div>
						</td>
					</tr>
					<tr >
						<td>
							<div >
							<label class="label_I">Téléphone :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="phone" placeholder="Télephone : 06 XX XX XX XX " value="<?php if (isset($_POST['phone'])) echo $phone; ?>" />
			                    <div><small><?php if (isset($error_phone)) echo $error_phone; ?></small></div>
			                </div>
						</td>
						<td>
							<div>
							<label >Photo :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="file" name="picture" accept="image/*" />
			                    <div><small><?php if (isset($error_picture)) echo $error_picture; ?></small></div>
			                </div>
						</td>
					</tr>
					<tr >
						<td >
							<div>
							<label>Décrivez-vous en quelque lignes :</label>
							</div>
				       		<div class="input_I">
			                    <textarea rows ="4" name="description" placeholder="Description"  >
			                	</textarea>
			                </div>
						</td>
						<td>
						</td>
					</tr>
				</table>
				<button type="submit" name="register" accept="image/*" > S'inscrire </button>
			</form>
			<footer>
			</footer>
	</body>
</html>
