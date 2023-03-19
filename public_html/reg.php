<?
require 'global.php';
ini_set('session.gc_maxlifetime','3600');
session_start();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
		<?
		if (! isset($_SESSION ["id"]))
		{
			$err_rl = $err_ml = $err_rg = $err_db = 0;
			if (isset ($_POST["chk"])) 
			{
				$rl = $_POST ['rlr'];
				$err_rl = ! $rl;
				$em = filter_var ($_POST ['em'], FILTER_VALIDATE_EMAIL);
				$err_ml = $em === "";
				if (! $err_rl && ! $err_ml)
				{
					$sql_srv = bv7dc_select_db ();
					$query = "CREATE TABLE IF NOT EXISTS `usrs` (
							`id` INT UNSIGNED NOT NULL AUTO_INCREMENT
							,`st` TINYINT UNSIGNED NOT NULL DEFAULT 0
							,`rl` TINYINT UNSIGNED NOT NULL DEFAULT 1
							,`pw` VARCHAR(32) NOT NULL
							,`em` VARCHAR(254) NOT NULL
							,`wm` VARCHAR(64) NOT NULL
							,`lg` VARCHAR(2) NOT NULL
							,`cnt_acc` BIGINT NOT NULL DEFAULT 0
							,`pay_acc` DOUBLE UNSIGNED NOT NULL DEFAULT 0
							,`cnt_prev` BIGINT NOT NULL DEFAULT 0
							,`pay_prev` DOUBLE UNSIGNED NOT NULL DEFAULT 0
							,`upd` BOOL NOT NULL DEFAULT '0'
							,`ua` VARCHAR(20) NOT NULL
							,`time` TIMESTAMP
							,`job_id` INT UNSIGNED NOT NULL
							,`pusr_id` INT UNSIGNED NOT NULL
							,`pr_add_pay` TINYINT NOT NULL DEFAULT 0
							,`prc_gh` DOUBLE UNSIGNED NOT NULL DEFAULT 0
							,`pr2` DOUBLE UNSIGNED NOT NULL DEFAULT 0
							,`wm2` VARCHAR(64) NOT NULL
							,`cnt2_reserv` DOUBLE NOT NULL DEFAULT 0
							,`pay2_reserv` DOUBLE NOT NULL DEFAULT 0
							,`timepay` DATETIME NOT NULL
							,`notify` BOOL NOT NULL
							,`unnotified` BOOL NOT NULL
							,`comment` VARCHAR(254) NOT NULL
							,`max_temp0` INT UNSIGNED NOT NULL
							,`max_temp1` INT UNSIGNED NOT NULL
							,`max_temp2` INT UNSIGNED NOT NULL
							,`max_temp3` INT UNSIGNED NOT NULL
							,`max_temp4` INT UNSIGNED NOT NULL
							,`max_temp5` INT UNSIGNED NOT NULL
							,`max_temp6` INT UNSIGNED NOT NULL
							,`max_temp7` INT UNSIGNED NOT NULL
							,`av_temp0` INT UNSIGNED NOT NULL
							,`av_temp1` INT UNSIGNED NOT NULL
							,`av_temp2` INT UNSIGNED NOT NULL
							,`av_temp3` INT UNSIGNED NOT NULL
							,`av_temp4` INT UNSIGNED NOT NULL
							,`av_temp5` INT UNSIGNED NOT NULL
							,`av_temp6` INT UNSIGNED NOT NULL
							,`av_temp7` INT UNSIGNED NOT NULL
							,`mhs0` INT UNSIGNED NOT NULL
							,`mhs1` INT UNSIGNED NOT NULL
							,`clreq_time` INT UNSIGNED NOT NULL
							,`mhs` INT UNSIGNED NOT NULL
							,`grp_id` INT UNSIGNED NOT NULL
							,`minusrs` TINYINT UNSIGNED NOT NULL
							,`priority` TINYINT NOT NULL
							,`v0` TINYINT UNSIGNED NOT NULL
							,`v2` TINYINT UNSIGNED NOT NULL
							,`v3` TINYINT UNSIGNED NOT NULL
							,`max_fan0` TINYINT UNSIGNED NOT NULL
							,`max_fan1` TINYINT UNSIGNED NOT NULL
							,`max_fan2` TINYINT UNSIGNED NOT NULL
							,`max_fan3` TINYINT UNSIGNED NOT NULL
							,`max_fan4` TINYINT UNSIGNED NOT NULL
							,`max_fan5` TINYINT UNSIGNED NOT NULL
							,`max_fan6` TINYINT UNSIGNED NOT NULL
							,`max_fan7` TINYINT UNSIGNED NOT NULL
							,`av_fan0` TINYINT UNSIGNED NOT NULL
							,`av_fan1` TINYINT UNSIGNED NOT NULL
							,`av_fan2` TINYINT UNSIGNED NOT NULL
							,`av_fan3` TINYINT UNSIGNED NOT NULL
							,`av_fan4` TINYINT UNSIGNED NOT NULL
							,`av_fan5` TINYINT UNSIGNED NOT NULL
							,`av_fan6` TINYINT UNSIGNED NOT NULL
							,`av_fan7` TINYINT UNSIGNED NOT NULL
							,`max_util0` TINYINT UNSIGNED NOT NULL
							,`max_util1` TINYINT UNSIGNED NOT NULL
							,`max_util2` TINYINT UNSIGNED NOT NULL
							,`max_util3` TINYINT UNSIGNED NOT NULL
							,`max_util4` TINYINT UNSIGNED NOT NULL
							,`max_util5` TINYINT UNSIGNED NOT NULL
							,`max_util6` TINYINT UNSIGNED NOT NULL
							,`max_util7` TINYINT UNSIGNED NOT NULL
							,`av_util0` TINYINT UNSIGNED NOT NULL
							,`av_util1` TINYINT UNSIGNED NOT NULL
							,`av_util2` TINYINT UNSIGNED NOT NULL
							,`av_util3` TINYINT UNSIGNED NOT NULL
							,`av_util4` TINYINT UNSIGNED NOT NULL
							,`av_util5` TINYINT UNSIGNED NOT NULL
							,`av_util6` TINYINT UNSIGNED NOT NULL
							,`av_util7` TINYINT UNSIGNED NOT NULL
							,`mh_acc` BIGINT NOT NULL
							,`mh_prev` BIGINT NOT NULL
							,`mu_f`TINYINT UNSIGNED NOT NULL
							,`mj`BIGINT NOT NULL DEFAULT -1
							,`time_ia` DATETIME NOT NULL
							,`notify_ia` BOOL NOT NULL
							,`unnotified_ia` BOOL NOT NULL
							,`prior_f` TINYINT NOT NULL
							,PRIMARY KEY (`id`)
							,INDEX(`st`)
							,INDEX(`rl`)
							,INDEX(`lg`)
							,INDEX(`pusr_id`)
							,INDEX`rl_nt_unntd`(`rl`,`notify`,`unnotified`)
							,INDEX`rl_ntia_unntdia`(`rl`,`notify_ia`,`unnotified_ia`)
					)";
					$res = mysql_query ($query);
					if (! $res) {
						exit(__FILE__.__LINE__);
					}
					$res = mysql_query ("SELECT `st` FROM `usrs` WHERE `em` = '{$em}' and `rl`<>1");
					$err_rg = mysql_num_rows ($res) > 0;
					if (! $err_rg)
					{
						$res = mysql_query("SELECT * FROM `usrs` WHERE `st`=1 AND `rl`&8 LIMIT 1");
						if (! $res)
							exit(__FILE__.__LINE__);
						if(! mysql_fetch_assoc($res)) {
							$pw = bv7pw_gen ();
							$res = mysql_query("
								INSERT `usrs`
								SET `st`=1
									,`rl`=8
									,`pw`='". md5 ($pw). "'
									,`em`='{$em}'
									,`wm`='{$_POST ['wm']}'
									,`lg`='{$_SESSION['lg']}'
							");
							if (! $res)
								exit(__FILE__.__LINE__);
							echo "Admin ID: ". mysql_insert_id(). "<br />";
							echo "Password: {$pw}<br />";
						} else {
							$err_db = ! mysql_query ("INSERT INTO `usrs` SET `rl` = {$rl}, `em` = '{$em}', `wm` = '{$_POST ['wm']}', `lg` = '". $_SESSION["lg"]. "'");
							if (! $err_db)
							{
							    bv7mails_adm ($rl & 4? 8: 12, "New User Registration", "Received an user registration application");
								$_SESSION ["id"] = 1;
							}
						}
					}
				    mysql_close ($sql_srv);
				}
			}
			if (! isset($_SESSION ["id"]))
			{
				?>
				<form action="reg.php" method="post">
					<table border>
						<caption>
							<?= bv7lg ("Fill out the registration information", "Заполните регистрационные данные") ?>:
							<font color="red">
								<?= $err_db? bv7lg ('Error: Add a user DB. Try to register later.', 'Ошибка добавления пользователя в БД. Попытайтесь зарегистрироваться позже.'): "" ?>
							</font>
						</caption>
						<tfoot>
							<tr>
								<td>
									<input type="hidden" name="chk" value="1" />
									<input type="submit" value="<?= bv7lg ("Registration", "Регистрация") ?>" />
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td>
									<table border>
										<caption>
											<font color="red">
												<?= $err_rl? bv7lg ("Must be selected at least one of the items", "Должен быть выбран хотя бы один из пунктов"): "" ?>
											</font>
										</caption>
										<tr>
											<td>
												<input type="radio" name="rlr" value="16"<?= $_POST ['rlr']==16? ' checked="checked"': '' ?> />
											</td>
											<td>
												<?= bv7lg ("I want to see statistics of my resources", "Я хочу просматривать статистику моих ресурсов") ?>
											</td>
										</tr>
										<?
										if(false){
											?>
											<tr>
												<td>
													<input type="radio" name="rlr" value="2"<?= $_POST ['rlr']==2? ' checked="checked"': '' ?> />
												</td>
												<td>
													<?= bv7lg ("I want to order calculations", "Я хочу заказать вычисления") ?>
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="rlr" value="4"<?= $_POST ['rlr']==4? ' checked="checked"': '' ?> />
												</td>
												<td>
													<?= bv7lg ("I want to become a member of the administration", "Я хочу стать членом администрации") ?>
												</td>
											</tr>
											<?
										}
										?>
									</table>
								<td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td colspan="2">
												<font color="red">
													<?= $err_ml? bv7lg ("Enter a valid email address", "Укажите правильный адрес электронной почты"): "" ?>
													<?= $err_rg? bv7lg ("Owner email address is already registered", "Владелец почтового адреса уже зарегистрирован"): "" ?>
												</font>
											</td>
										</tr>
										<tr>
											<td align="right">
												<?= bv7lg ("E-mail address", "Адрес электронной почты") ?>
											</td>
											<td>
												<input type="text" name="em" value="<?= $_POST ['em'] ?>" />
											</td>
										</tr>
										<tr>
											<td align="right">
												<?= bv7lg ("Purse", "Кошелек") ?>
											</td>
											<td>
												<input type="text" name="wm" value="<?= $_POST ['wm'] ?>" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				<?
			}
		}
		if (isset($_SESSION ["id"])) echo bv7lg ("We will review the application and send you authentication information", "Мы рассмотрим заявку и вышлем Вам аутентификационные данные"). "<br />";
		?>
	</body>
</html>