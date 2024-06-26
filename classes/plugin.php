<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main class for plugin 'media_kinescope'
 *
 * @package   media_kinescope
 * @copyright 2023 LMS-Service {@link https://lms-service.ru/}
 * @author    Nikita Badin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Player that embeds Kinescope links
 *
 * @package   media_kinescope
 * @copyright 2023 LMS-Service {@link https://lms-service.ru/}
 * @author    Nikita Badin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_kinescope_plugin extends core_media_player_external {
    protected function embed_external(moodle_url $url, $name, $width, $height, $options) {
        $videoid = $this->get_video_id();
        $info = s($name);

        // Note: resizing via url is not supported, user can click the fullscreen
        // button instead. iframe embedding is not xhtml strict but it is the only
        // option that seems to work on most devices.
        self::pick_video_size($width, $height);

        $output = <<<OET
<span class="mediaplugin mediaplugin_kinescope">
<iframe title="$info" src="https://kinescope.io/embed/$videoid"
width="$width" height="$height" frameborder="0"
webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</span>
OET;

        return $output;
    }

    /**
     * Default rank
     * @return int
     */
    public function get_rank() {
        return 1100;
    }

    protected function get_video_id(): string {
        return $this->get_video_id_with_code() ?? $this->matches[1] ?? '';
    }

    protected function get_video_id_with_code(): ?string {
        $id = $this->matches[2] ?? null;

        if (!empty($id)) {
            $code = $this->matches[3] ?? null;
            if (!empty($code)) {
                return "{$id}?h={$code}";
            }

            return $id;
        }

        return null;
    }

    protected function get_regex() {
        $start = '~^https?://kinescope.io/';
        // Middle bit: either abqaz12345 or abqaz12345/abqaz12345.
        $middle = '(([0-9a-z]+)/([0-9a-z]+)|[0-9a-z]+)';
        return $start . $middle .core_media_player_external::END_LINK_REGEX_PART;
    }

    public function get_embeddable_markers() {
        return ['kinescope.io/'];
    }
}
