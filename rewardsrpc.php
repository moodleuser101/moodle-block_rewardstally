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
 * This is an RPC file associated with the Moodle plugin block_rewardstally.
 *
 * This can be placed either on a remote server or on the local Moodle server and used to access some data held
 * 'outside' of the Moodle sphere of influence. It constructs a JSON response to an RPC call from the Moodle block.
 * Note, the methods in this file could be replicated in any language since the Moodle plugin is ultimately
 * language-independent - any alternative implementation must simply replicate the same JSON structure,
 * API_SECRET check and POST fields.
 *
 * @package    block_rewardstally
 * @category   admin
 * @copyright  2021 P Reid
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$apisecret = 'F20EA6F40B174F468DDC276050CBE821AD7FDB9C85023E2D07829B43DCC6865D';
// Securty token used to ensure API only responds to a valid request.
// Should be an SHA-256 string.

$queryuser = false;
$userid = 0;
$utally = -1;


// First we check the API token is valid.
if (isset($_REQUEST["api_token"])) {
    $token = filter_input(INPUT_POST, "api_token", FILTER_SANITIZE_STRING);
    if (!($token === $apisecret)) {
        block_rewardstally_reject_token();
    }
} else {
    block_rewardstally_reject_token();
}
if (isset($_REQUEST["userid"])) {
    $queryuser = true;
    $userid = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_STRING);
}

if ($queryuser && block_rewardstally_check_studentid($userid)) {
    $utally = block_rewardstally_query_user($userid);
}

$rewardsdata = array();
if ($utally > -1) {
    $rewardsdata["usertally"] = $utally;
}
$rewardsdata["timestamp"] = block_rewardstally_set_validity();
$rewardsdata["communities"] = block_rewardstally_query_communities();

echo json_encode($rewardsdata);

/**
 * Throws an error message to the output stream and ceases termination of the PHP script
 * on the grounds that the supplied API token is invalid
 */
function block_rewardstally_reject_token() {
    header("HTTP/1.1 401 Unauthorized");
    echo("401 Unauthorized: Check API token");
    die();
}

/**
 * Queries the external data store and returns the
 * points tally associated with the given user id, or -1 if user was not found
 * @param string $user user ID to query
 * @return int count of the user's rewards tally/point score, or -1 if not relevant.
 */
function block_rewardstally_query_user($user) {
    /*
     * Some specific code will need to go in here to conduct the extraction of the data.
     * block_rewardstally_check_studentid() should already have validated the *format*
     * of the user ID. In the event of the user ID being not found in the database call or
     * otherwise not relevant, return -1.
     */

    // For demo purposes, we just return a random number.
    return rand(32, 7373);
}

/**
 * Queries the external data store and turns an array of
 * community objects, where each community object contains a name, a cumulative count of the
 * community's point score and an associated hexadecimal/HTML colour code (no # prefix) for
 * the community.
 * @return array community objects, each community object is an array with keys 'name', 'count', 'colour'
 */
function block_rewardstally_query_communities() {
    /*
     * An example is shown below; the database routine etc should go here and construct a valid array to return;
     */
    $com1 = array(
        "name" => "Enterprise",
        "count" => 655,
        "colour" => "ff0000"
    );
    $com2 = array(
        "name" => "Challenger",
        "count" => 553,
        "colour" => "2E64FE"
    );
    $com3 = array(
        "name" => "Discovery",
        "count" => 223,
        "colour" => "088A29"
    );
    $com4 = array(
        "name" => "Endeavour",
        "count" => 2111,
        "colour" => 'F5ECCE'
    );
    $allcommunities = array();
    array_push($allcommunities, $com1, $com2, $com3, $com4);
    return $allcommunities;
}

/**
 * Date of reference associated with the rewards data returned. Eg if rewards data is accumulated each day at midnight, there
 * is no benefit to having a time code and instead, use yesterday's date. The string here should be returned in the format
 * desired for the end-user, it will be prefixed with "Valid as at" (or equivalent) in the GUI.
 * @return string the validity date/time in desired human-readable format
 */
function block_rewardstally_set_validity() {
    $default = date("H:i d-M-Y");
    /*
     * Conduct some sort of database call or use the default as 'now' to ascertain the
     * time to which the data relates.
     *
     */
    return $default;
}

/**
 * Performs regular expression and other such checks on the supplied ID to ascertain
 * whether or not this ID corresponds to a student ID. For most schools, rewards
 * points are awarded to students, not staff, so it would be pointless to query for rewards
 * points for an ID that corresponded to a staff member.
 * @param string $id supplied user ID that needs to be checked
 * @return boolean whether or not this ID likely corresponds to a student.
 */
function block_rewardstally_check_studentid($id) {
    /*
     * Institutions should alter this method to meet the needs of users in their
     * context and setting. Sample code is included here.
     */
    /*
     * This sample code assumes a school is using the UK's 'Unique Pupil Number' (UPN)
     * as the ID number for linking different IT systems together within a school. Thus, a
     * user with a valid UPN number is a student, and a user without a valid UPN is likely a staff member.
     */
    $id = trim(strtoupper($id)); // Convert to upper case if it isn't already for performing checks and remove whitespace.
    if (strlen($id) !== 13) {
        return false; // UPNs are 13 characters long.
    }
    if (preg_match("/[^A-Z0-9]/", $id) > 0) {
        // Fail straight away: any non-letter/non-digit in a UPN is bad.
        return false;
    }
    $numletters = 0;
    for ($i = 0; $i < strlen($id); $i++) {
        // Look at each letter in the id; see if it is a letter, increment the $numletters.
        $char = substr($id, $i, 1);
        if (preg_match("/[A-Z]/", $char) > 0) {
            $numletters++;
        }
    }
    if ($numletters !== 1) {
        return false; // UPNs have precisely one letter.
    }
    return true;
}
