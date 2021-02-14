<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Language definition file (English strings) for the block_rewardstally plugin.
 *
 * @package    block_rewardstally
 * @category   admin
 * @copyright  2021 P Reid
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Rewards Tally';
$string['rewardstallyl:addinstance'] = 'Add a new Rewards Tally block';
$string['rewardstally:myaddinstance'] = 'Add a new Rewards Tally block to the My Moodle page';
$string['blockheader'] = "Block header";
$string['blockheaderdesc'] = "Text to display as the 'header' for the block throughout the site";
$string['headerconfig'] = "Rewards Tally global configuration"; // Admin/settings header.
$string['descconfig'] = "Configures the global settings relating to the Rewards Tally block that impact all users throughout the site.";
$string['labelshowindiv'] = "Show individual rewards";
$string['descshowindiv'] = "When checked, the individual user rewards tally will be shown, if relevant to their context (eg for students but not for staff).";
$string['remoteurl'] = "Rewards API URL";
$string['remoteurldesc'] = "URL of the remote data source that returns a JSON array containing all of the rewards data to be displayed by the block.";
$string['remoteurlsecret'] = "Rewards API Secret";
$string['remoteurlsecretdesc'] = "The SHA-256 API secret used by the remote rewards API to authenticate requests for data; you could use eg https://passwordsgenerator.net/sha256-hash-generator/ to generate one.";
$string['mytally'] = "My achievement";
$string['usertally'] = "User tally prefix";
$string['usertallydesc'] = 'The text to display in the block in front of the user rewards tally';
$string['validasat'] = "Valid as at";
$string['verifytls'] = 'Verify TLS certificate';
$string['verifytlsdesc'] = 'When checked, the TLS/SSL certificate (if using an HTTPS URL) of the remote rewards data URL will be verified using the Moodle server CACERTS certificate authority pool (set in PHP). Some networks using proxy servers or non-standard TLS procedures may have to update their PHP CACERTs pool, or disable this option. Some people would consider it a security risk to disable this option; opinions will vary.';
$string['sortcommunity'] = "Sort community rewards tally";
$string['sortcommunitydesc'] = "When set, the rewards tallies for the different communities will be sorted in descending order, to list a 'leaderboard-style' of each community. Otherwise, each community will be listed in the order that its data is presented by the remote data source.";
$string['userfield'] = "User ID field";
$string['userfielddesc'] = "Moodle user field containing the relevant user ID to send to the external data store; if your institution uses different types of user IDs in differenet contexts, be sure to select the correct field name that will be recognised by the external data store. This is not necessarily the standard Moodle user ID. Common values: 'id' for the raw Moodle ID or 'idnumber' for the secondary user ID field. Must be a valid field in the Moodle users table.";
$string['privacy:metadata:rewards_api'] = 'In order to integrate with the remote data source providing the rewards data, user data needs to be exchanged with that service.';
$string['privacy:metadata:rewards_api:userid'] = 'The userid is sent from Moodle to allow a user to see the total of their rewards points to date.';