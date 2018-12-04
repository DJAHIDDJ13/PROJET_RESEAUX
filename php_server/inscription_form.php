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
	
		<center>
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
			<form  method="post" action="" class="inscription"  >
				<p class="title">Inscription!</p>
				<table>
					<tr>
						<td>
							<div >
							<label >Login:</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="username" placeholder="Login" value="<?php if (isset($username)) echo $username; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label style="margin-right:11.30cm ">Mail</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="mail" placeholder="Mail" value="<?php if (isset($mail)) echo $mail; ?>" required="required"/>
			                </div>
							
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Mot de Pass :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="password" placeholder="Mot de Pass" value="<?php if (isset($password)) echo $password; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label >Confirmer Mot de Pass :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="password" name="c_password" placeholder="Confirmer Mot de Pass" value="<?php if (isset($c_password)) echo $c_password; ?>" required="required"/>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Nom :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="lastname" placeholder="Nom" value="<?php if (isset($lastname)) echo $lastname; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label >Prenom :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="firstname" placeholder="Prenom" value="<?php if (isset($firstname)) echo $firstname; ?>" required="required"/>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Date De Naissance :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="date" name="birthday"  value="<?php if (isset($birthday)) echo $birthday; ?>" required="required"/>
			                </div>
						</td>
						<td>
							<div >
							<label >Ville :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="text" name="city" placeholder="Ville" value="<?php if (isset($city)) echo $city; ?>" />
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label>Téléphone :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="Number" name="phone" placeholder="Télephone" value="<?php if (isset($phone)) echo $phone; ?>" />
			                </div>
						</td>
						<td>
							<div>
							<label >Photo :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="file" name="image" />
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label>Décrivez- vous en quelque lignes :</label>
							</div>
				       		<div class="input_I">
			                    <input  type="textarea" name="username" placeholder="Login" value="<?php if (isset($username)) echo $username; ?>" required="required"/>
			                </div>
						</td>
					</tr>
				</table>
				<button type="submit" class="bouttonConnect"> Se connecter </button>
			</form>
		</center>
	</body>
	<footer>
	</footer>
</html>
