<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('rki/user_id', get_string('user_id', 'block_rki'), "", null, PARAM_INT));
    $settings->add(new admin_setting_configtext('rki/user_token', get_string('user_token', 'block_rki'), "", null));

}