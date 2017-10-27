<?php 
require_once dirname(__FILE__) . '/core/bootstrap.php';
require_once dirname(__FILE__) . '/makePOT/makepot.php';

require_once dirname(__FILE__) . '/languages.php';
require_once dirname(__FILE__) . '/threads.php';
require_once dirname(__FILE__) . '/members.php';
require_once dirname(__FILE__) . '/options.php';
require_once dirname(__FILE__) . '/template.php';
require_once dirname(__FILE__) . '/social_auth.php';
require_once dirname(__FILE__) . '/theme.php';
require_once dirname(__FILE__) . '/widget.php';
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/front.php';
require_once dirname(__FILE__) . '/customizer.php';
require_once dirname(__FILE__) . '/update.php';

// for mobile
require_once TEMPLATEPATH . '/mobile/functions.php';