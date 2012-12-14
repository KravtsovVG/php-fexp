-- INDENTING (emacs/vi): -*- mode:sql; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:

/** PHP File Exchange Platform (PHP-FEXP)
 *
 * <P><B>COPYRIGHT:</B></P>
 * <PRE>
 * PHP File Exchange Platform (PHP-FEXP)
 * Copyright (C) 2012 Cedric Dufour <http://cedric.dufour.name>
 * Author(s): Cedric Dufour <cedric.dufour@network.net>
 *
 * This file is part of the PHP File Exchange Platform (PHP-FEXP).
 *
 * The PHP File Exchange Platform (PHP-FEXP) is free software:
 * you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation, Version 3.
 *
 * The PHP File Exchange Platform (PHP-FEXP) is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License (LICENSE.TXT) for more details.
 * </PRE>
 *
 * @package    FEXP
 * @subpackage SQL
 * @copyright  2012 Cedric Dufour <http://cedric.dufour.name>
 * @author     Cedric Dufour <cedric.dufour@ced-network.net>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    @version@
 * @link       http://cedric.dufour.name/software/php-fexp
 */


/*
 * TABLE: tb_FEXP_File
 * INFO:  Uploaded files
 ********************************************************************************/

/*
 * SCHEMA
 */

-- Table (and Primary Key)
-- DROP TABLE tb_FEXP_File;
CREATE TABLE tb_FEXP_File ( pk integer NOT NULL AUTO_INCREMENT PRIMARY KEY ) ENGINE=InnoDB;

-- Data
ALTER TABLE tb_FEXP_File ADD COLUMN vc_File_hash varchar(64) NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN vc_File_name varchar(192) NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN i_File_size integer NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN vc_File_md5 varchar(32) NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN ts_Upload timestamp NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN vc_Upload_user varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN vc_Upload_ip varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN ts_Expire timestamp NOT NULL;
ALTER TABLE tb_FEXP_File ADD COLUMN b_Option_public boolean NOT NULL DEFAULT false;
ALTER TABLE tb_FEXP_File ADD COLUMN b_Option_unique boolean NOT NULL DEFAULT false;
ALTER TABLE tb_FEXP_File ADD COLUMN b_Option_multiple boolean NOT NULL DEFAULT false;
ALTER TABLE tb_FEXP_File ADD COLUMN i_Download_usercount integer NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_File ADD COLUMN i_Download_count integer NOT NULL DEFAULT 0;
-- ... indexes
ALTER TABLE tb_FEXP_File ADD INDEX ix__tb_FEXP_File__vc_File_hash ( vc_File_hash );
ALTER TABLE tb_FEXP_File ADD INDEX ix__tb_FEXP_File__ts_Upload ( ts_Upload );

/*
 * TRIGGERS
 */

-- ... before insert
DROP TRIGGER IF EXISTS tg__tb_FEXP_File__bi;
DELIMITER $
CREATE DEFINER = CURRENT_USER TRIGGER tg__tb_FEXP_File__bi BEFORE INSERT ON tb_FEXP_File
FOR EACH ROW 
BEGIN

  -- Data consistency checks
  IF NEW.b_Option_public OR NEW.b_Option_multiple THEN
    SET NEW.b_Option_unique = false;
  END IF;

END $
DELIMITER ;

-- ... before update
DROP TRIGGER IF EXISTS tg__tb_FEXP_File__bu;
DELIMITER $
CREATE DEFINER = CURRENT_USER TRIGGER tg__tb_FEXP_File__bu BEFORE UPDATE ON tb_FEXP_File
FOR EACH ROW
BEGIN

  -- Data consistency checks
  IF NEW.b_Option_public OR NEW.b_Option_multiple OR NEW.i_Download_usercount > 1 THEN
    SET NEW.b_Option_unique = false;
  END IF;

END $
DELIMITER ;

/*
 * FUNCTIONS
 */

-- ... upload
DROP FUNCTION IF EXISTS fn_FEXP_File_upload;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_File_upload(
  _vc_File_hash text,
  _vc_File_name text,
  _i_File_size integer,
  _vc_File_md5 text,
  _ts_Upload timestamp,
  _vc_Upload_user text,
  _vc_Upload_ip text,
  _ts_Expire timestamp,
  _b_Option_public boolean,
  _b_Option_unique boolean,
  _b_Option_multiple boolean
) RETURNS integer
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __return integer;
  SELECT pk FROM tb_FEXP_File WHERE vc_File_hash = _vc_File_hash INTO __return;

  IF NOT __return IS NULL THEN

    UPDATE tb_FEXP_File SET
      vc_File_name = _vc_File_name,
      i_File_size = _i_File_size,
      vc_File_md5 = _vc_File_md5,
      ts_Upload = _ts_Upload,
      vc_Upload_user = _vc_Upload_user,
      vc_Upload_ip = _vc_Upload_ip,
      ts_Expire = _ts_Expire,
      b_Option_public = _b_Option_public,
      b_Option_unique = _b_Option_unique,
      b_Option_multiple = _b_Option_multiple
    WHERE vc_File_hash = _vc_File_hash;

  ELSE

    INSERT INTO tb_FEXP_File (
      vc_File_hash,
      vc_File_name,
      i_File_size,
      vc_File_md5,
      ts_Upload,
      vc_Upload_user,
      vc_Upload_ip,
      ts_Expire,
      b_Option_public,
      b_Option_unique,
      b_Option_multiple
    ) VALUES (
      _vc_File_hash,
      _vc_File_name,
      _i_File_size,
      _vc_File_md5,
      _ts_Upload,
      _vc_Upload_user,
      _vc_Upload_ip,
      _ts_Expire,
      _b_Option_public,
      _b_Option_unique,
      _b_Option_multiple
    );

    SET __return = LAST_INSERT_ID();

  END IF;

  RETURN __return;

END $
DELIMITER ;

-- ... update
DROP FUNCTION IF EXISTS fn_FEXP_File_update;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_File_update(
  _pk integer,
  _b_Option_public boolean,
  _b_Option_unique boolean,
  _b_Option_multiple boolean
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN

  UPDATE tb_FEXP_File SET
    b_Option_public = _b_Option_public,
    b_Option_unique = _b_Option_unique,
    b_Option_multiple = _b_Option_multiple
  WHERE
    pk = _pk
  ;

  RETURN true;

END $
DELIMITER ;

-- ... delete
DROP FUNCTION IF EXISTS fn_FEXP_File_delete;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_File_delete(
  _pk integer
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN

  DELETE FROM tb_FEXP_File
  WHERE
    pk = _pk
  ;

  RETURN ROW_COUNT() >= 1;

END $
DELIMITER ;


/*
 * TABLE: tb_FEXP_Access
 * INFO:  Access Control List
 ********************************************************************************/

/*
 * SCHEMA
 */

-- Table (and Primary Key)
-- DROP TABLE tb_FEXP_Access;
CREATE TABLE tb_FEXP_Access ( fk integer NOT NULL ) ENGINE=InnoDB;
-- ... constraints
ALTER TABLE tb_FEXP_Access ADD CONSTRAINT fk__tb_FEXP_Access__fk_FEXP_File FOREIGN KEY ( fk ) REFERENCES tb_FEXP_File ( pk ) ON DELETE CASCADE;
-- ... indexes
ALTER TABLE tb_FEXP_Access ADD INDEX ix__tb_FEXP_Access__fk ( fk );

-- Data
ALTER TABLE tb_FEXP_Access ADD COLUMN vc_Download_user varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_Access ADD COLUMN vc_Download_ip varchar(96) NOT NULL DEFAULT '';
ALTER TABLE tb_FEXP_Access ADD COLUMN ts_Download_start timestamp NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Access ADD COLUMN ts_Download_progress timestamp NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Access ADD COLUMN i_Download_progress integer NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Access ADD COLUMN ts_Download_complete timestamp NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Access ADD COLUMN i_Download_count integer NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Access ADD COLUMN b_Download_block boolean NOT NULL DEFAULT false;
-- ... indexes
ALTER TABLE tb_FEXP_Access ADD INDEX ix__tb_FEXP_Access__vc_Download_user ( fk, vc_Download_user );

/*
 * TRIGGERS
 */

-- ... after insert
DROP TRIGGER IF EXISTS tg__tb_FEXP_Access__ai;
DELIMITER $
CREATE DEFINER = CURRENT_USER TRIGGER tg__tb_FEXP_Access__ai AFTER INSERT ON tb_FEXP_Access
FOR EACH ROW 
BEGIN

  UPDATE tb_FEXP_File SET
    i_Download_usercount = i_Download_usercount + 1
  WHERE pk = NEW.fk;

END $
DELIMITER ;

-- ... after delete
DROP TRIGGER IF EXISTS tg__tb_FEXP_Access__ad;
DELIMITER $
CREATE DEFINER = CURRENT_USER TRIGGER tg__tb_FEXP_Access__ad AFTER DELETE ON tb_FEXP_Access
FOR EACH ROW 
BEGIN

  UPDATE tb_FEXP_File SET
    i_Download_usercount = i_Download_usercount - 1
  WHERE pk = OLD.fk;

END $
DELIMITER ;

/*
 * FUNCTIONS
 */

-- ... authorize
DROP FUNCTION IF EXISTS fn_FEXP_Access_authorize;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Access_authorize(
  _fk integer,
  _vc_Download_user text,
  _ts_Expire timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __exists boolean;
  SELECT true FROM tb_FEXP_Access WHERE fk = _fk AND vc_Download_user = _vc_Download_user INTO __exists;

  IF __exists THEN

    UPDATE tb_FEXP_Access SET
      b_Download_block = false  -- if downloader is authorized again, unblock him
    WHERE fk = _fk AND vc_Download_user = _vc_Download_user;

  ELSE

    INSERT INTO tb_FEXP_Access (
      fk,
      vc_Download_user
    ) VALUES (
      _fk,
      _vc_Download_user
    );

    IF ROW_COUNT() < 1 THEN
      RETURN false;
    END IF;

  END IF;

  UPDATE tb_FEXP_File SET
    ts_Expire = GREATEST( ts_Expire, _ts_Expire )
  WHERE pk = _fk;

  RETURN true;

END $
DELIMITER ;

-- ... block
DROP FUNCTION IF EXISTS fn_FEXP_Access_block;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Access_block(
  _fk integer,
  _vc_Download_user text
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN

  UPDATE tb_FEXP_Access SET
    b_Download_block = true
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user;

  RETURN true;

END $
DELIMITER ;

-- ... start
DROP FUNCTION IF EXISTS fn_FEXP_Access_start;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Access_start(
  _fk integer,
  _vc_Download_user text,
  _vc_Download_ip text,
  _ts_Download_start timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN

  UPDATE tb_FEXP_Access SET
    vc_Download_ip = _vc_Download_ip,
    ts_Download_start = _ts_Download_start,
    ts_Download_progress = _ts_Download_start,
    i_Download_progress = 0,
    ts_Download_complete = 0
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user;

  RETURN true;

END $
DELIMITER ;

-- ... progress
DROP FUNCTION IF EXISTS fn_FEXP_Access_progress;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Access_progress(
  _fk integer,
  _vc_Download_user text,
  _ts_Download_progress timestamp,
  _i_Download_progress integer
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN

  UPDATE tb_FEXP_Access SET
    ts_Download_progress = _ts_Download_progress,
    i_Download_progress = _i_Download_progress
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user;

  RETURN true;

END $
DELIMITER ;

-- ... complete
DROP FUNCTION IF EXISTS fn_FEXP_Access_complete;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Access_complete(
  _fk integer,
  _vc_Download_user text,
  _ts_Download_complete timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __size integer;
  SELECT i_File_Size FROM tb_FEXP_File WHERE pk = _fk INTO __size;

  UPDATE tb_FEXP_Access SET
    ts_Download_progress = _ts_Download_complete,
    i_Download_progress = __size,
    ts_Download_complete = _ts_Download_complete,
    i_Download_count = i_Download_count + 1
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user;

  UPDATE tb_FEXP_File SET
    i_Download_count = i_Download_count + 1
  WHERE pk = _fk;

  RETURN true;

END $
DELIMITER ;


/*
 * TABLE: tb_FEXP_Log
 * INFO:  Access History
 ********************************************************************************/

/*
 * SCHEMA
 */

-- Table (and Primary Key)
-- DROP TABLE tb_FEXP_Log;
CREATE TABLE tb_FEXP_Log ( fk integer NOT NULL ) ENGINE=InnoDB;
-- ... indexes
ALTER TABLE tb_FEXP_Log ADD INDEX ix__tb_FEXP_Log__fk ( fk );

-- Data
ALTER TABLE tb_FEXP_Log ADD COLUMN vc_File_name varchar(192) NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN i_File_size integer NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN ts_Upload timestamp NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN vc_Upload_user varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN vc_Upload_ip varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN vc_Download_user varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN vc_Download_ip varchar(96) NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN ts_Download_start timestamp NOT NULL;
ALTER TABLE tb_FEXP_Log ADD COLUMN ts_Download_progress timestamp NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Log ADD COLUMN i_Download_progress integer NOT NULL DEFAULT 0;
ALTER TABLE tb_FEXP_Log ADD COLUMN ts_Download_complete timestamp NOT NULL DEFAULT 0;
-- ... indexes
ALTER TABLE tb_FEXP_Log ADD INDEX ix__tb_FEXP_Log__vc_Download_user ( fk, vc_Download_user, vc_Download_ip, ts_Download_start );

/*
 * FUNCTIONS
 */

-- ... create
DROP FUNCTION IF EXISTS fn_FEXP_Log_create;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Log_create(
  _fk integer,
  _vc_Download_user text,
  _vc_Download_ip text,
  _ts_Download_start timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __exists boolean;
  SELECT true FROM tb_FEXP_Log WHERE fk = _fk AND vc_Download_user = _vc_Download_user AND vc_Download_ip = _vc_Download_ip AND ts_Download_start = _ts_Download_start INTO __exists;

  IF __exists IS NULL THEN

    INSERT INTO tb_FEXP_Log (
      fk,
      vc_File_name,
      i_File_size,
      ts_Upload,
      vc_Upload_user,
      vc_Upload_ip,
      vc_Download_user,
      vc_Download_ip,
      ts_Download_start
    )
    SELECT
      _fk,
      vc_File_name,
      i_File_size,
      ts_Upload,
      vc_Upload_user,
      vc_Upload_ip,
      _vc_Download_user,
      _vc_Download_ip,
      _ts_Download_start
    FROM tb_FEXP_File WHERE pk = _fk;

    RETURN ROW_COUNT() >= 1;

  END IF;

  RETURN false;

END $
DELIMITER ;

-- ... start
DROP FUNCTION IF EXISTS fn_FEXP_Log_start;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Log_start(
  _fk integer,
  _vc_Download_user text,
  _vc_Download_ip text,
  _ts_Download_start timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __create boolean;
  SELECT fn_FEXP_Log_create( _fk, _vc_Download_user, _vc_Download_ip, _ts_Download_start ) INTO __create;

  IF NOT __create THEN

    UPDATE tb_FEXP_Log SET
      ts_Upload = ts_Upload,  -- DON'T ASK ME!!! Without this line, ts_Upload gets updated to CURRENT_TIMESTAMP...
      ts_Download_progress = _ts_Download_start,
      i_Download_progress = 0,
      ts_Download_complete = 0
    WHERE fk = _fk AND vc_Download_user = _vc_Download_user AND ts_Download_start = _ts_Download_start;

  END IF;

  RETURN true;

END $
DELIMITER ;

-- ... progress
DROP FUNCTION IF EXISTS fn_FEXP_Log_progress;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Log_progress(
  _fk integer,
  _vc_Download_user text,
  _vc_Download_ip text,
  _ts_Download_start timestamp,
  _ts_Download_progress timestamp,
  _i_Download_progress integer
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __create boolean;
  SELECT fn_FEXP_Log_create( _fk, _vc_Download_user, _vc_Download_ip, _ts_Download_start ) INTO __create;

  UPDATE tb_FEXP_Log SET
    ts_Upload = ts_Upload,  -- DON'T ASK ME!!! Without this line, ts_Upload gets updated to CURRENT_TIMESTAMP...
    ts_Download_progress = _ts_Download_progress,
    i_Download_progress = _i_Download_progress
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user AND ts_Download_start = _ts_Download_start;

  RETURN true;

END $
DELIMITER ;

-- ... complete
DROP FUNCTION IF EXISTS fn_FEXP_Log_complete;
DELIMITER $
CREATE
DEFINER = CURRENT_USER
FUNCTION fn_FEXP_Log_complete(
  _fk integer,
  _vc_Download_user text,
  _vc_Download_ip text,
  _ts_Download_start timestamp,
  _ts_Download_complete timestamp
) RETURNS boolean
LANGUAGE SQL
NOT DETERMINISTIC
MODIFIES SQL DATA
SQL SECURITY DEFINER
BEGIN
  DECLARE __create boolean;
  SELECT fn_FEXP_Log_create( _fk, _vc_Download_user, _vc_Download_ip, _ts_Download_start ) INTO __create;

  UPDATE tb_FEXP_Log SET
    ts_Upload = ts_Upload,  -- DON'T ASK ME!!! Without this line, ts_Upload gets updated to CURRENT_TIMESTAMP...
    ts_Download_progress = _ts_Download_complete,
    i_Download_progress = i_File_size,
    ts_Download_complete = _ts_Download_complete
  WHERE fk = _fk AND vc_Download_user = _vc_Download_user AND ts_Download_start = _ts_Download_start;

  RETURN true;

END $
DELIMITER ;
