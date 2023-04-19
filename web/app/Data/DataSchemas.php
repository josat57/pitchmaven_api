<?php


namespace PitchMaven\Data;
/**
 * Pre-defines the database schema for auto creation on request.
 * PHP Version 8.1.6
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <josephsamuelw1@gmail.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.coms.com
 */


/**
 * Database Schema class
 * PHP Version 8.1.6
 *
 * @category API
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinpiri Samuel Joseph <josephsamuelw1@gmail.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.coms.com
 */
class DataSchemas
{
    private static $_schema;

    /**
     * Class Constructor
     *
     * @param string $schema schema to create
     *
     * @return mix
     */
    public function __construct(string $schema = null)
    {
        self::$_schema = $schema;
    }

    /**
     * Database schemas
     *
     * @return string 
     */
    public static function dbschema(): String
    {
        $response = "A schema must be specified";
        if (empty(self::$_schema)) {
            $res = "A schema must be specified";
            return json_encode($res);
        }

        switch (strtolower(self::$_schema)) {

        case "pm_admins_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_admins_tbl(
            id INT AUTO_INCREMENT NOT NULL,
            user_name VARCHAR(200) NULL,
            first_name VARCHAR(200) NOT NULL,
            last_name VARCHAR(200) NOT NULL,
            password VARCHAR(200) NOT NULL,
            email VARCHAR(200) NOT NULL,
            phone_no VARCHAR(20) NULL,
            role VARCHAR(200) NULL,
            position VARCHAR(200) NULL,
            status INT DEFAULT 0,
            created TIMESTAMP,
            PRIMARY KEY (id)
            )";
            break;

        case "pm_users_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_users_tbl(
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(256) DEFAULT NULL,
            last_name VARCHAR(256) DEFAULT NULL,
            other_name VARCHAR(256) DEFAULT NULL,
            email VARCHAR(256) DEFAULT NULL,
            role VARCHAR(20) DEFAULT NULL,
            dob Date DEFAULT NULL,
            street VARCHAR(200) DEFAULT NULL,
            city VARCHAR(200) DEFAULT NULL,
            state VARCHAR(200) DEFAULT NULL,
            country VARCHAR(50) DEFAULT NULL,
            place_of_birth VARCHAR(200) DEFAULT NULL,
            marital_status VARCHAR(20) DEFAULT NULL,
            gender VARCHAR(10) DEFAULT NULL,
            mobile VARCHAR(20) DEFAULT NULL,
            status INT(11) UNSIGNED DEFAULT 0,
            is_verified INT(11) DEFAULT 0,
            is_active INT(11) DEFAULT 0,
            is_locked INT(11) DEFAULT 0,
            confirm_code INT(11) DEFAULT NULL,
            user_name VARCHAR(2048) DEFAULT NULL,
            password VARCHAR(2048) DEFAULT NULL,
            accept_policy VARCHAR(10) DEFAULT NULL,
            profile_photo VARCHAR(500) DEFAULT NULL,
            profile_path VARCHAR(500) DEFAULT NULL,
            created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break;

        case "pm_players_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_players_tbl(
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(256) DEFAULT NULL,
            last_name VARCHAR(256) DEFAULT NULL,
            other_name VARCHAR(256) DEFAULT NULL,
            guardian_first_name VARCHAR(256) DEFAULT NULL,
            guardian_last_name VARCHAR(256) DEFAULT NULL,
            guardian_address VARCHAR(256) DEFAULT NULL,
            guardian_phone VARCHAR(256) DEFAULT NULL,
            email VARCHAR(256) DEFAULT NULL,
            role VARCHAR(20) DEFAULT NULL,
            dob Date DEFAULT NULL,
            street VARCHAR(200) DEFAULT NULL,
            team_id INT(11) DEFAULT NULL,
            feet_orientation VARCHAR(200) DEFAULT NULL,
            position VARCHAR(200) DEFAULT NULL,
            city VARCHAR(200) DEFAULT NULL,
            state VARCHAR(200) DEFAULT NULL,
            country VARCHAR(50) DEFAULT NULL,
            place_of_birth VARCHAR(200) DEFAULT NULL,
            marital_status VARCHAR(20) DEFAULT NULL,
            gender VARCHAR(10) DEFAULT NULL,
            mobile VARCHAR(20) DEFAULT NULL,
            status INT(11) UNSIGNED DEFAULT 0,
            is_verified INT(11) DEFAULT 0,
            is_active INT(11) DEFAULT 0,
            is_locked INT(11) DEFAULT 0,
            confirm_code INT(11) DEFAULT NULL,
            user_name VARCHAR(2048) DEFAULT NULL,
            password VARCHAR(2048) DEFAULT NULL,
            accept_policy VARCHAR(10) DEFAULT NULL,
            profile_photo VARCHAR(500) DEFAULT NULL,
            profile_path VARCHAR(500) DEFAULT NULL,
            created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break; 
            
        case "pm_players_features_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_players_features_tbl(
                id INT NOT NULL AUTO_INCREMENT,
                feet_orientation VARCHAR(200) NOT NULL,
                height DECIMAL(10, 1) NOT NULL,
                weight DECIMAL(10, 1) NOT NULL,
                position_on_field VARCHAR(200) NOT NULL,
                player_id INT NOT NULL,
                PRIMARY KEY (id)
            )";
            break;

        case "pm_teams_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_teams_tbl(
                team_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                team_name VARCHAR(500) DEFAULT NULL,
                team_logo VARCHAR(255) DEFAULT NULL,
                description LONGTEXT DEFAULT NULL,
                team_manager VARCHAR(255) DEFAULT NULL,
                team_coach VARCHAR(200) DEFAULT NULL,
                team_slogan VARCHAR(255) DEFAULT NULL,
                uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break;

        case "pm_tournament_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_tournament_tbl(
                id int UNSIGNED NOT NULL AUTO_INCREMENT,
                tournament_name VARCHAR(200) NOT NULL,
                description LONGTEXT NULL,
                number_of_teams INT NOT NULL DEFAULT 0,
                host INT NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                city VARCHAR(200) DEFAULT NULL,
                state VARCHAR(200) DEFAULT NULL,
                country VARCHAR(200) DEFAULT NULL,
                trophy_title VARCHAR(200) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT TIMESTAMP,
                PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break;

        case "pm_tournament_awards_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_tournament_awards_tbl(
                id INT NOT NULL AUTO_INCREMENT,
                award_title VARCHAR(200) NOT NULL DEFAULT NULL,
                award_description LONGTEXT,
                awarded_to INT NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT TIMESTAMP,
                tournament_id INT NOT NULL,
                PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break;

        case "pm_sessions_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_sessions_tbl(
            id INT NOT NULL AUTO_INCREMENT,
            session_id VARCHAR(200) NOT NULL,
            user_id VARCHAR(200) NOT NULL,
            session_token VARCHAR(1000) NULL,
            user_ip VARCHAR(200),
            user_agent VARCHAR(500),
            session_time VARCHAR(200),
            session_elapsed INT NULL,
            session_status INT NULL,
            created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(id)
            )ENGINE = InnoDB DEFAULT CHARSET = utf8;
            ";
            break;

        case "pm_messages_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_messages_tbl(
                msg_id INT NOT NULL AUTO_INCREMENT,
                subject VARCHAR(500) NOT NULL,
                message LONGTEXT NOT NULL,
                status INT DEFAULT NULL,
                sender VARCHAR(200) DEFAULT NOT NULL,
                recipient INT DEFAULT NOT NULL,
                response LONGTEXT,
                seen_date TIMESTAMP,
                sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )";
            break;

        case "pm_fixtures_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_fixtures_tbl(
                fixture_id INT NOT NULL AUTO_INCREMENT,
                match_date DATE DEFAULT NULL,
                match_time TIME DEFAULT NULL,
                home_team VARCHAR(200) DEFAULT NULL,
                away_team VARCHAR(200) DEFAULT NULL,
                match_round INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )";
            break;

        case "pm_scores_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_scores_tbl(
                score_id INT NOT NULL AUTO_INCREMENT,
                home_team VARCHAR(200) DEFAULT NULL,
                away_team VARCHAR(200) DEFAULT NULL,
                fixture INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )";
            break;

        case "pm_tournament_pitch_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_tournament_pitch_tbl(
                id INT NOT NULL AUTO_INCREMENT,
                pitch_name VARCHAR(200) NOT NULL DEFAULT NULL,
                pitch_capacity INT DEFAULT NULL,
                pitch_location VARCHAR(200) NOT NULL DEFAULT NULL,
                description LONGTEXT DEFAULT NULL,
                tournament_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT TIMESTAMP,
                PRIMARY KEY (id)
            )";
            break;

        case "pm_payments_tbl":
            $response = "
            CREATE TABLE IF NOT EXISTS pm_payments_tbl(
                id INT NOT NULL AUTO_INCREMENT,
                firstname VARCHAR(200) DEFAULT NULL,
                lastname VARCHAR(200) DEFAULT NULL,
                email VARCHAR(200) DEFAULT NULL,
                phonenumber VARCHAR(200) DEFAULT NULL,
                country VARCHAR(200) DEFAULT NULL,
                amount DECIMAL(12,2) DEFAULT NULL,
                currency VARCHAR(20) DEFAULT NULL,
                transaction_id VARCHAR(200) DEFAULT NULL,
                tx_ref VARCHAR(200) DEFAULT NULL,
                flw_ref VARCHAR(200) DEFAULT NULL,
                device_fingerprint VARCHAR(200) DEFAULT NULL,
                charged_amount VARCHAR(200) DEFAULT NULL,
                app_fee VARCHAR(200) DEFAULT NULL,
                merchant_fee VARCHAR(200) DEFAULT NULL,
                processor_response VARCHAR(200) DEFAULT NULL,
                auth_model VARCHAR(200) DEFAULT NULL,
                ip VARCHAR(200) DEFAULT NULL,
                narration VARCHAR(1000) DEFAULT NULL,
                status VARCHAR(200) DEFAULT NULL,
                payment_type VARCHAR(200) DEFAULT NULL,
                card_created_at VARCHAR(200) DEFAULT NULL,
                account_id VARCHAR(200) DEFAULT NULL,
                amount_settled VARCHAR(200) DEFAULT NULL,
                first_6digits VARCHAR(200) DEFAULT NULL,
                last_4digits VARCHAR(200) DEFAULT NULL,
                issuer VARCHAR(200) DEFAULT NULL,
                type VARCHAR(200) DEFAULT NULL,
                token VARCHAR(1000) DEFAULT NULL,
                expiry VARCHAR(200) DEFAULT NULL,
                don_id VARCHAR(200) DEFAULT NULL,
                payment_date VARCHAR(200) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id)
            )";
            break;
        }
        return $response;
    }
}
