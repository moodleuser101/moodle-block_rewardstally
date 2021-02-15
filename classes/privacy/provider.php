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
 * Privacy definitions for the rewardstally project
 *
 * @package    block_rewardstally
 * @category   admin
 * @copyright  2021 P Reid
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_rewardstally\privacy;
use core_privacy\local\metadata\collection;
defined('MOODLE_INTERNAL') || die();

/**
 * Implements the Moodle privacy API
 */
class provider implements
// This plugin does store personal user data.
\core_privacy\local\metadata\provider {

    /**
     * Called by the Moodle Privacy API interface.
     * @param collection $collection Moodle privacy collection object.
     * @return collection The updated Moodle privacy collection object.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_external_location_link('rewards_api', [
            'userid' => 'privacy:metadata:rewards_api:userid',
            ], 'privacy:metadata:lti_client');

        return $collection;
    }

}