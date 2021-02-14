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

if ($queryuser) {
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
 * @return int count of the user's rewards tally/point score
 */
function block_rewardstally_query_user($user) {
    /*
     * Some specific code will need to go in here to conduct the extraction of the data.
     * It would also be wise to validate the userid in $user, and perhaps determine
     * whether a tally is relevant to this user's context; eg if the supplied user ID
     * is that of a "teacher", then return -1.
     */

    // For demo purposes, we just return the userid; need to return the actual count.
    return $user;
}

/**
 * Queries the external data store and turns an array of
 * community objects, where each community object contains a name, a cumulative count of the
 * community's point score and an associated hexadecimal/HTML colour code (no # prefix) for
 * the community.
 * @return array an array of community objects, each community object is an array with keys 'name', 'count', 'colour'
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
