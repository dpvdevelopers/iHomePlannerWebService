<?php
	require 'config.php';

	function create_new_user($mail, $pass, $ip, $conn){
		$array_mail = explode('@',$mail, 3);
		$user = $array_mail[0];
		echo $user;
		echo '<br>';
		echo $pass;
		save_log_connection($ip, $user, $conn);
		database_creation($user, $conn);
		create_database_user($user, $pass, $conn);
	}
	function save_log_connection($ip,$user, $conn){
		$sentencia = $conn->prepare("INSERT INTO reg(ip, user, date) VALUES (?, ?, ?)");
		$sentencia->bind_param('sss', $ip, $user, date('Y-m-j'));
		$sentencia->execute();
	}
	function create_database_user($user, $pass, $conn){
		$conn->query("CREATE USER '" . $user . "'@'localhost' IDENTIFIED BY '" . $pass . "' ;");

		$conn->query("GRANT ALL PRIVILEGES ON " . $user . ".* TO " . $user . " @'localhost';");
		$conn->query("GRANT USAGE ON  " . $user . ".* TO " . $user . " @'%' IDENTIFIED BY '" . $pass ."' ;");
		$conn->query("FLUSH PRIVILEGES;");

	}
	function database_creation($user, $conn){
		/*Establezco autocommit a false para crear una transacción*/
		/*$conn->autocommit(false);*/
		/*Primero creamos la base de datos*/
		$conn->query("CREATE DATABASE IF NOT EXISTS " . $user . " CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';");
		/*Creo la estructura de tablas de la base de datos*/
		$conn->query("SET UNIQUE_CHECKS=0;");
		$conn->query("SET GLOBAL FOREIGN_KEY_CHECKS=0;");
		$conn->query("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';");
		$conn->query("USE " . $user . ";");
		$conn->query("CREATE TABLE IF NOT EXISTS Licenses("
			. "LicenseId INT NOT NULL, "
			. "LicenseName VARCHAR(45) NOT NULL, "
			. "LicensePrice DECIMAL(6,2) NOT NULL, "
			. "LicenseDays INT NOT NULL, "
			. "LicenseUserLimit INT NOT NULL, "
			. "PRIMARY KEY (LicenseId)) ENGINE = InnoDB;"
			);


		$conn->query("CREATE TABLE IF NOT EXISTS UserLicense("
			. "UserId DOUBLE NOT NULL, "
			. "LicenseId INT NOT NULL DEFAULT 0, "
			. "InitDate DATE NOT NULL, "
			. "EndDate DATE NULL, "
			. "PRIMARY KEY(UserId,LicenseId)) ENGINE = InnoDB; "
			);
/*
			. "INDEX FK_USERLICENSE_LICENSES_idx (LicenseId ASC) VISIBLE, "
			. "CONSTRAINT FK_USERLICENSE_USERS "
			. "FOREIGN KEY (UserId) "
			. "REFERENCES " . $user . ".Users (UserId) "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT FK_USERLICENSE_LICENSES "
			. "FOREIGN KEY (LicenseId) "
			. "REFERENCES " . $user . ".Licenses (LicenseId) "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/

		$conn->query("CREATE TABLE IF NOT EXISTS Groups("
			. "GroupId DOUBLE NOT NULL, "
			. "GroupName VARCHAR(45) NOT NULL, "
			. "GroupTotalMembers INT NOT NULL DEFAULT 5, "
			. "GroupLicense INT NOT NULL DEFAULT 0, "
			. "PRIMARY KEY (GroupId)) "
			/*. "INDEX FK_GROUPS_USERLICENSE_idx (GroupLicense ASC) VISIBLE, "
			. "CONSTRAINT FK_GROUPS_USERLICENSE "
			. "FOREIGN KEY (GroupLicense) "
			. "REFERENCES UserLicense (LicenseId) "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
			*/
			. "ENGINE = InnoDB;"
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Users ("
			. "UserId DOUBLE NOT NULL, "
			. "UserLevel INT NOT NULL, "
			. "UserLicense INT NOT NULL DEFAULT 0, "
			. "UserName VARCHAR(100) NOT NULL, "
			. "UserLastName VARCHAR(150) NOT NULL, "
			. "UserMail VARCHAR(80) NOT NULL, "
			. "UserGroup DOUBLE NOT NULL, "
			. "PRIMARY KEY (UserId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_USERS_GROUPS_idx' (UserGroup' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_USERS_GROUPS' "
			. "FOREIGN KEY ('UserGroup') "
			. "REFERENCES '" . $user . "'.'Groups' ('GroupId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Entry ("
			. "EntryId DOUBLE NOT NULL, "
			. "EntryName VARCHAR(45) NOT NULL, "
			. "IncomeAmount DECIMAL(12,2) NOT NULL, "
			. "EntryDate DATETIME NOT NULL, "
			. "EntryUser DOUBLE NOT NULL, "
			. "EntryIsIncome TINYINT NOT NULL DEFAULT 0, "
			. "EntryDetail VARCHAR(150) NULL, "
			. "EntryCurrency VARCHAR(45) NULL, "
			. "PRIMARY KEY (EntryId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_INCOME_USERS_idx' ('EntryUser' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_INCOME_USERS' "
			. "FOREIGN KEY ('EntryUser') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB; "
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Tasks ("
			. "TaskId DOUBLE NOT NULL, "
			. "TaskUser DOUBLE NOT NULL, "
			. "TaskTitle VARCHAR(45) NOT NULL, "
			. "TaskDetail VARCHAR(300) NULL, "
			. "TaskDate DATETIME NOT NULL, "
			. "TaskEndDate DATETIME NOT NULL, "
			. "TaskFullDay TINYINT NULL DEFAULT 0, "
			. "TaskDuration DECIMAL NOT NULL DEFAULT 0.0, "
			. "TaskAsignedUser DOUBLE NULL, "
			. "PRIMARY KEY (TaskId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_TASKS_USERS_idx' ('TaskUser' ASC) VISIBLE, "
			. "INDEX 'FK_TASK_ASIGNED_USER_idx' ('TaskAsignedUser' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_TASKS_USERS' "
			. "FOREIGN KEY ('TaskUser') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_TASK_ASIGNED_USER' "
			. "FOREIGN KEY ('TaskAsignedUser') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE ) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Events ("
			. "EventId DOUBLE NOT NULL, "
			. "EventUser DOUBLE NOT NULL, "
			. "EventTitle VARCHAR(45) NOT NULL, "
			. "EventDetail VARCHAR(300) NULL, "
			. "EventFullDay TINYINT NOT NULL DEFAULT 0, "
			. "EventDate DATETIME NOT NULL, "
			. "EventIsPublic TINYINT NOT NULL DEFAULT 0, "
			. "EventCategory VARCHAR(45) NOT NULL, "
			. "PRIMARY KEY (EventId)) ENGINE = InnoDB;"
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Users_has_Events ("
			. "Users_UserId DOUBLE NOT NULL, "
			. "Events_EventId DOUBLE NOT NULL, "
			. "Event_Owner DOUBLE NOT NULL, "
			. "PRIMARY KEY (Users_UserId, Events_EventId)) ENGINE  = InnoDB; "
/*			. "INDEX 'FK_USERS_HAS_EVENTS_USERS1_idx' ('Users_UserId' ASC) VISIBLE, "
			. "INDEX 'FK_USERS_HAS_EVENTS_EVENTS1_idx' ('Events_EventId' ASC) VISIBLE, "
			. "INDEX 'FK_UHE_USERS_idx' ('Event_Owner' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_USERS_HAS_EVENTS_USERS1' "
			. "FOREIGN KEY ('Users_UserId') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON DELETE CASCADE, "
			. "CONSTRAINT 'FK_USERS_HAS_EVENTS_EVENTS1' "
			. "FOREIGN KEY ('Events_EventId') "
			. "REFERENCES '" . $user . "'.'Events' ('EventId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_UHE_USERS' "
			. "FOREIGN KEY ('Event_Owner') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS ShoppingCard ("
			. "ShoppingCardId DOUBLE NOT NULL, "
			. "ShoppingCardOwner DOUBLE NOT NULL, "
			. "ShoppingCardIsPublic TINYINT NOT NULL DEFAULT 0, "
			. "ShoppingCardDate DATETIME NULL, "
			. "PRIMARY KEY (ShoppingCardId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_SHOPPINGCARD_USERS_idx' ('ShoppingCardOwner' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_SHOPPINGCARD_USERS' "
			. "FOREIGN KEY  ('ShoppingCardOwner') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Users_has_ShoppingCard ("
			. "Users_UserId DOUBLE NOT NULL, "
			. "ShoppingCard_ShoppingCardId DOUBLE NOT NULL, "
			. "PRIMARY KEY (Users_UserId, ShoppingCard_ShoppingCardId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_USERS_HAS_SHOPPINGCARD_SHOPPINGCARD1_idx' ('ShoppingCard_ShoppingCardId' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_USERS_HAS_SHOPPINGCARD_USERS1' "
			. "FOREIGN KEY ('Users_UserId') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_USERS_HAS_SHOPPINGCARD_SHOPPINGCARD1' "
			. "FOREIGN KEY ('ShoppingCard_ShoppingCardId') "
			. "REFERENCES '" . $user . "'.'ShoppingCard' ('ShoppingCardId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Products ("
			. "ProductId DOUBLE NOT NULL, "
			. "ProductName VARCHAR(45) NOT NULL, "
			. "ProductBarcode VARCHAR(50) NULL, "
			. "ProductDetail VARCHAR(150) NULL, "
			. "ProductPrice DECIMAL NOT NULL, "
			. "PRIMARY KEY (ProductId)) ENGINE = InnoDB;"
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Line_ShoppingCard ("
			. "ProductId DOUBLE NOT NULL, "
			. "ShoppingCardId DOUBLE NOT NULL, "
			. "ProductQuantity DECIMAL NOT NULL , "
			. "PRIMARY KEY (ProductId,ShoppingCardId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_PRODUCTS_HAS_SHOPPPINGCARD_SHOPPINGCARD1_idx' ('ShoppingCardId' ASC) VISIBLE, "
			. "INDEX 'FK_PRODUCTS_HAS_SHOPPINGCARD_PRODUCTS1_idx' ('ProductId' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_PRODUCTS_HAS_SHOPPINGCARD_PRODUCTS1' "
			. "FOREIGN KEY ('ProductId') "
			. "REFERENCES '" . $user . "'.'Products' ('ProductId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_PRODUCTS_HAS_SHOPPINGCARD_SHOPPINGCARD1' "
			. "FOREIGN KEY ('ShoppingCardId') "
			. "REFERENCES '" . $user . "'.'ShoppingCard' ('ShoppingCardId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE); ENGINE = InnoDB"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Vehicles ("
			. "VehicleId DOUBLE NOT NULL, "
			. "VehicleBrand VARCHAR(45) NULL, "
			. "VehicleModel VARCHAR(100) NULL, "
			. "VehicleSerialNumber VARCHAR(45) NULL, "
			. "VehicleDate DATE NOT NULL, "
			. "VehicleType VARCHAR(45) NULL, "
			. "VehicleOwner DOUBLE NOT NULL, "
			. "VehicleDetails VARCHAR(200), "
			. "VehicleKms INT NULL, "
			. "PRIMARY KEY (VehicleId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_VEHICLES_USERS_idx' ('VehicleOwner' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_VEHICLES_USERS' "
			. "FOREIGN KEY ('VehicleOwner') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Maintenance ("
			. "MaintenanceId DOUBLE NOT NULL, "
			. "MaintenanceVehicle DOUBLE NOT NULL, "
			. "MaintenanceIsPeriodic TINYINT NOT NULL DEFAULT 0, "
			. "MaintenancePeriodicityDays INT NULL, "
			. "MaintenancePeriodicityKms INT NULL, "
			. "MaintenanceType VARCHAR(150) NULL, "
			. "MaintenanceName VARCHAR(100) NOT NULL, "
			. "MaintenanceDetail VARCHAR(400) NULL, "
			. "MaintenancePrice DECIMAL NULL, "
			. "PRIMARY KEY (MaintenanceId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_MAINTENANCE_VEHICLES_idx' ('MaintenanceVehicle' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_MAINTENANCE_VEHICLES' "
			. "FOREIGN KEY ('MaintenanceVehicle') "
			. "REFERENCES '" . $user . "'.'Vehicles' ('VehicleId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE); ENGINE = InnoDB"
*/
			);

		$conn->query("CREATE TABLE IF NOT EXISTS Maintenance_Operation ("
			. "Maintenance_MaintenanceId DOUBLE NOT NULL, "
			. "Users_UserId DOUBLE NOT NULL, "
			. "ExpectedDate DATETIME NOT NULL, "
			. "OperationDate DATETIME NULL, "
			. "OperationIsClosed TINYINT NOT NULL DEFAULT 0, "
			. "VehicleId DOUBLE NULL, "
			. "PRIMARY KEY (Maintenance_MaintenanceId,Users_UserId)) ENGINE = InnoDB; "
/*			. "INDEX 'FK_MAINTENANCE_HAS_USERS_USERS1_idx' ('Users_UserId' ASC) VISIBLE, "
			. "INDEX 'FK_MAINTENANCE_HAS_USERS_MAINTENANCE1_idx' ('Maintenance_MaintenanceId' ASC) VISIBLE, "
			. "INDEX 'FK_MO_VEHICLES_idx' ('Vehicle_id' ASC) VISIBLE, "
			. "CONSTRAINT 'FK_MAINTENANCE_HAS_USERS_MAINTENANCE1' "
			. "FOREIGN KEY ('Maintenance_MaintenanceId') "
			. "REFERENCES '" . $user . "'.'Maintenance' ('MaintenanceId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_MAINTENANCE_HAS_USERS_USERS1' "
			. "FOREIGN KEY ('Users_UserId') "
			. "REFERENCES '" . $user . "'.'Users' ('UserId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE, "
			. "CONSTRAINT 'FK_MO_VEHICLES' "
			. "FOREIGN KEY ('Vehicle_id') "
			. "REFERENCES '" . $user . "'.'Vehicles' ('VehicleId') "
			. "ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE = InnoDB;"
*/
			);

/*		$conn->query("SET SQL_MODE=@OLD_SQL_MODE;");
		$conn->query("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;");
		$conn->query("SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;");
		Ejecuto todas las sentencias con un commit
/*		if(!$conn->commit()) {
			echo "Falló la creación de la base de datos";
		}
		$conn->autocommit(true);
*/

	}

?>
