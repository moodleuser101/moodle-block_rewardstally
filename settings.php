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
 * Settings file controlling site-wide/admin settings of the rewardstally block
 *
 * @package    block_rewardstally
 * @category   admin
 * @copyright  2021 P Reid
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading(
        'block_rewardstally/headerconfig',
        get_string('headerconfig', 'block_rewardstally'),
        get_string('descconfig', 'block_rewardstally')
)); // Specifies the sub-heading that appears under 'Site administration'.

$settings->add(new admin_setting_configtext(
        'block_rewardstally/blockheader',
        get_string('blockheader', 'block_rewardstally'),
        get_string('blockheaderdesc', 'block_rewardstally'),
        get_string('pluginname', 'block_rewardstally'),
        PARAM_TEXT,
        20
)); // Specifies the name of the block as presented to the end user.

$settings->add(new admin_setting_configcheckbox(
        'block_rewardstally/showindividual',
        get_string('labelshowindiv', 'block_rewardstally'),
        get_string('descshowindiv', 'block_rewardstally'),
        '1'
)); // Setting controlling whether or not to show individual user tallies.

$settings->add(new admin_setting_configtext(
        'block_rewardstally/mytallytext',
        get_string('usertally', 'block_rewardstally'),
        get_string('usertallydesc', 'block_rewardstally'),
        get_string('mytally', 'block_rewardstally'),
        PARAM_TEXT,
        30
)); // Specifies how individual user tallies should be labelled.

$settings->add(new admin_setting_configtext(
        'block_rewardstally/useridfield',
        get_string('userfield', 'block_rewardstally'),
        get_string('userfielddesc', 'block_rewardstally'),
        "idnumber",
        PARAM_TEXT,
        30
)); // Specifies which field of the Moodle user table should be used to ascertain the user ID.


$settings->add(new admin_setting_configtext(
        'block_rewardstally/remoteurl',
        get_string('remoteurl', 'block_rewardstally'),
        get_string('remoteurldesc', 'block_rewardstally'),
        "https://localhost/example-url",
        PARAM_URL,
        99
)); // URL of a remote (or potentially local) server providing the data service.

$settings->add(new admin_setting_configtext(
        'block_rewardstally/remoteurlsecret',
        get_string('remoteurlsecret', 'block_rewardstally'),
        get_string('remoteurlsecretdesc', 'block_rewardstally'),
        "F20EA6F40B174F468DDC276050CBE821AD7FDB9C85023E2D07829B43DCC6865D",
        PARAM_TEXT,
        64
)); // API secret key to send to the remote URL.

$settings->add(new admin_setting_configcheckbox(
        'block_rewardstally/verifytls',
        get_string('verifytls', 'block_rewardstally'),
        get_string('verifytlsdesc', 'block_rewardstally'),
        '1'
)); // Whether or not to verify the TLS certificate of the remote URL.

$settings->add(new admin_setting_configcheckbox(
        'block_rewardstally/sortcommunityrewards',
        get_string('sortcommunity', 'block_rewardstally'),
        get_string('sortcommunitydesc', 'block_rewardstally'),
        '1'
)); // Whether or not to sort the 'community' point tallies in descending order.
