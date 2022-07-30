#Track all DB changes here
#date"2022-07-29"
ALTER TABLE users
DROP COLUMN phone;

#date"2022-07-30"
CREATE TABLE `ems`.`reset_password` (`id` INT(11) NOT NULL AUTO_INCREMENT , `userId` INT(11) NOT NULL , `hashKey` VARCHAR(255) NOT NULL , `isActive` TINYINT NOT NULL , `createdOn` VARCHAR(20) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
