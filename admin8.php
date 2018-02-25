<?php
/**
* @version   $Id$
* @package   Admin Forever
* @copyright Copyright (C) 2008 - 2010 Edvard Ananyan. All rights reserved.
* @copyright Copyright (C) 2018 ecfirm.net. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

/**
 * Admin Forever plugin
 */
class  plgSystemAdmin8 extends CMSPlugin {
    /**
     * Constructor
     *
     * @access protected
     * @param  object $subject The object to observe
     * @param  array  $config  An array that holds the plugin configuration
     * @since  1.0
     */
    function __construct(& $subject, $config) {
        // check to see if the user is admin
        $user = Factory::getUser();
        if(!$user->authorise('manage', 'com_banners')) return;

        parent::__construct($subject, $config);
    }

    /**
     * Add JavaScript reloader
     * @access public
     */
    function onAfterRender() {

        $timeout = intval(Factory::getApplication()->get('lifetime') * 60 / 3 * 1000);
        $url = Uri::base() . 'index.php?option=com_cpanel';

        $javascript = <<<EOM

        <script type="text/javascript">
        var req = false;
        function refreshSession() {
            req = false;
            if(window.XMLHttpRequest && !(window.ActiveXObject)) {
                try {
                    req = new XMLHttpRequest();
                } catch(e) {
                    req = false;
                }
            // branch for IE/Windows ActiveX version
            } else if(window.ActiveXObject) {
                try {
                    req = new ActiveXObject("Msxml2.XMLHTTP");
                } catch(e) {
                    try {
                        req = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch(e) {
                        req = false;
                    }
                }
            }

            if(req) {
                req.onreadystatechange = processReqChange;
                req.open("HEAD", "$url", true);
                req.send();
            }
        }

        function processReqChange() {
            // only if req shows "loaded"
            if(req.readyState == 4) {
                // only if "OK"
                if(req.status == 200) {
                    // TODO: think what can be done here
                } else {
                    // TODO: think what can be done here
                    //alert("There was a problem retrieving the XML data: " + req.statusText);
                }
            }
        }

        setInterval("refreshSession()", $timeout);
        </script>

EOM;

        $app = Factory::getApplication();
        $content = $app->getBody();
        $content = str_replace('</body>', $javascript . '</body>', $content);
        $app->setBody($content);

    }
}