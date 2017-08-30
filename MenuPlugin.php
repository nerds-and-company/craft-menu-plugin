<?php

namespace Craft;

/**
 * Menu Plugin.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2017, Nerds & Company
 */
class MenuPlugin extends BasePlugin
{
    /**
     * Return plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Menu');
    }

    /**
     * Return plugin version.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Return plugin developer.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Nerds & Company';
    }

    /**
     * Return plugin developer url.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://nerds.company';
    }
}
