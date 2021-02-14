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
 * Rewards Tally plugin
 *
 * This plugin provides a block mechanism for displaying 'Rewards' points awarded to both the
 * individual user (in the context of a student) and/or the cumulative awards achieved by the community.
 * This plugin is predicated on the idea that 'rewards' are presented as a running numerical tally/count,
 * and that this data is stored or processed on some external system. This plugin provides mechanisms to make
 * calls to an external (or potentially, internal) web server and receive a JSON response, which is then parsed
 * and displayed in the Moodle block.
 *
 * @package    block_rewardstally
 * @category   admin
 * @copyright  2021 P Reid
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_login();

/**
 * Extends the standard Moodle 'block' class to create a custom block plugin
 *
 * Used to display rewards data for the user and their community.
 */
class block_rewardstally extends block_base {

    /**
     * Initialise the class
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_rewardstally');
    }

    /**
     * Called by Moodle to retrieve the user-facing HTML content of the block
     * @return Moodle content object array
     */
    public function get_content() {
        global $USER;
        $rewardsdata = $this->block_rewardstally_getremotedata();
        if ($this->content !== null) {
            return $this->content;
        }
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }
        $this->content = new stdClass;

        if (isset($rewardsdata["timestamp"])) {
            $this->content->footer = "<span class='small text-center'>" .
                get_string('validasat', 'block_rewardstally') . " " .
                $rewardsdata["timestamp"] . "</span>";
        } else {
            $this->content->footer = "No timestamp found";
        }

        $myoutput = "<table class='table table-compact table-hover'>\n<tobdy>";
        // Check if the admin setting permits showing individual user tallies.
        if (get_config('block_rewardstally', 'showindividual')) {
            if (isset($rewardsdata["usertally"])) {
                $usertally = $rewardsdata["usertally"];
                if (is_numeric($usertally) && ($usertally > -1)) {
                    $myoutput .= "\n<tr><td class='font-weight-bold'>" .
                        get_config('rewardstally', 'mytallytext') . "</td><td class='text-right'>" .
                        $rewardsdata["usertally"] . "</td></tr>";
                }
            }
        }

        if (isset($rewardsdata["communities"])) {
            if (get_config('block_rewardstally', 'sortcommunityrewards')) {
                /*
                 * We should sort the community reward points in descending order,
                 * so that we get a 'leader board' style of output when rendered.
                 */
                $columns = array_column($rewardsdata["communities"], "count");
                array_multisort($columns, SORT_DESC, $rewardsdata["communities"]);
            }
            // Loop through each community; ascertain their name, count and colour.
            foreach ($rewardsdata["communities"] as $community) {
                if (isset($community["name"]) && isset($community["count"]) && isset($community["colour"])) {
                    // Community seems well-defined, add its details to the output.
                    // Firstly, validate the colour code, or set it to plain white background.
                    if ($this->block_rewardstally_validatecolour($community["colour"])) {
                        $colour = $community["colour"];
                    } else {
                        $colour = "000000";
                    }
                    /*
                     * WE need two potential text colours - dark and light respectively,
                     * to act as foreground colours. We compute whether the proposed background is
                     * 'light' or 'dark' and apply the opposite foreground colour.
                     */
                    $textcolour = "#000000";
                    if ($this->block_rewardstally_getbrightness($colour) < 130) {
                        $textcolour = "#FFFFFF";
                    }

                    $myoutput .= "\n<tr><td><span class='badge' style='color:" .
                        $textcolour . "; background-color: #" .
                        $colour . "; font-size: 90%;'>" .
                        $community["name"] . "</span></td>"
                        . "<td class='text-right'>" . $community["count"] . "</td></tr>";
                }
            }
        }

        $myoutput .= "\n</tbody>\n</table>\n";
        $this->content->text = $myoutput;
        return $this->content;
    }

    /**
     * Called by Moodle on block plugins to determine whether multiple instances of the same block can be displayed
     * @return boolean whether or not to allow multiple instances
     */
    public function instance_allow_multiple() {
        return false; // There is no need for multiple instances of this block on any page.
    }

    /**
     * Sets site-wide configuration back-end
     * @return boolean whether to use site-wide configuration options
     */
    public function has_config() {
        return true; // Enables use of the site-wide admin console to set settings.
    }

    /**
     * Determines which types of Moodle page can display this block (or *ADD* the block)
     * @return array Moodle page types
     */
    public function applicable_formats() {
        return array(
            'site' => true,
            'site-index' => true,
            'course-view' => false,
            'mod' => false,
            'my' => true,
            'my-index' => true
        );
    }

    /**
     * Initiates a remote procedure call and populates a JS array from JSON
     * @return array 3-part array containing the remote data
     */
    private function block_rewardstally_getremotedata() {
        global $USER; // For accessing user ID fields etc.
        $url = get_config('block_rewardstally', 'remoteurl');
        $secret = get_config('block_rewardstally', 'remoteurlsecret');
        $post = array(
            "api_token" => $secret
        );
        if (get_config('block_rewardstally', 'showindividual')) {
            $field = get_config('block_rewardstally', 'useridfield');
            if (isset($USER->{$field})) {
                $post["userid"] = $USER->{$field};
            } else {
                $post["userid"] = -1;
            }
        }

        $verifytls = get_config('block_rewardstally', 'verifytls');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $verifytls); // SSL verify?
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $returndata = curl_exec($curl);
        curl_close($curl);
        return json_decode($returndata, true);
    }

    /**
     * Takes a user-supplied hexadecimal string and ascertains whether or not it can be used to form an HTML colour code stirng.
     * @param string $colour
     * @return boolean whether or not the supplied $colour can be used as an HTML colour code
     */
    private function block_rewardstally_validatecolour($colour) {
        if (strlen($colour) !== 6) {
            return false;
        }
        return ctype_xdigit($colour);
    }

    /**
     * Determines whether a colour code is 'light' or 'dark' to help ascertain what
     * type of background/foreground colour combination is required.
     * @param string $hex the hexadecimal colour code
     * @return int colour brightness value on a 0-255 scale
     */
    private function block_rewardstally_getbrightness($hex) {
        // Returns brightness value from 0 to 255 after strip off any leading #.
        $hex = str_replace('#', '', $hex);
        $cr = hexdec(substr($hex, 0, 2));
        $cg = hexdec(substr($hex, 2, 2));
        $cb = hexdec(substr($hex, 4, 2));
        return (($cr * 299) + ($cg * 587) + ($cb * 114)) / 1000;
    }

}