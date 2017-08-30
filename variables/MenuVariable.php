<?php

namespace Craft;

/**
 * Loohuis Menu Variable.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2017, Nerds & Company
 */
class MenuVariable
{
    /**
     * @var array
     */
    private $menu = array();

    /**
     * @var array
     */
    private $breadcrumbs = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (craft()->request->isSiteRequest()) {
            $singles = $this->getSingleDataByLocale();
            foreach ($singles as $single) {
                if ($single['uri'] == '__home__') {
                    $single['uri'] = '/';
                }
                $single['url'] = UrlHelper::getSiteUrl($single['uri']);
                $this->menu[$single['handle']] = (object) $single;
            }
        }
    }

    /**
     * Get menu item.
     *
     * @param string $handle
     *
     * @return object
     */
    public function get($handle)
    {
        return $this->menu[$handle];
    }

    /**
     * Get menu link.
     *
     * @param string $handle
     *
     * @return string
     */
    public function getLink($handle)
    {
        return TemplateHelper::getRaw('<a href="'.$this->get($handle)->url.'">'.$this->get($handle)->title.'</a>');
    }

    /**
     * Check whether menu item is active.
     *
     * @param string $handle
     *
     * @return bool
     */
    public function isActive($handle)
    {
        $path = craft()->request->path;
        $uri = $this->get($handle)->uri;

        return $uri == '/' ? empty($path) : strpos('/'.$path, '/'.$uri) !== false;
    }

    /**
     * Get breadcrumbs.
     *
     * @param EntryModel $entry
     *
     * @return array
     */
    public function getBreadcrumbs(EntryModel $entry)
    {
        // Generate singles breadcrumb
        $this->getSinglesBreadcrumbs();

        // Add entry breadcrumb
        $this->getEntryBreadcrumb($entry);

        return $this->breadcrumbs;
    }

    /**
     * Get single data by locale.
     *
     * @return array
     */
    private function getSingleDataByLocale()
    {
        return craft()->db->createCommand()
            ->select(array('e.id', 's.handle', 'l.uri', 'c.title'))
            ->from('entries e')
            ->join('sections s', 'e.sectionId = s.id')
            ->join('elements_i18n l', 'e.id = l.elementId')
            ->join('content c', 'e.id = c.elementId AND l.locale = c.locale')
            ->where('s.type = :type', array(':type' => 'single'))
            ->andWhere('l.locale = :locale', array('locale' => craft()->language))
            ->group('e.id')
            ->queryAll();
    }

    /**
     * Get breadcrumbs from singles.
     */
    private function getSinglesBreadcrumbs()
    {
        $slugs = explode('/', craft()->request->path);

        for ($i = 1; $i <= count($slugs); ++$i) {
            $slug = implode('/', array_slice($slugs, 0, $i));
            foreach ($this->menu as $single) {
                if ($single->uri == $slug) {
                    $this->breadcrumbs[] = $single;
                }
            }
        }
    }

    /**
     * Get breadcrumb from entry.
     *
     * @param EntryModel $entry
     */
    private function getEntryBreadcrumb(EntryModel $entry)
    {
        $last = array_slice($this->breadcrumbs, -1)[0];

        if ($last->uri != $entry->uri) {
            $this->breadcrumbs[] = array(
                'id' => $entry->id,
                'handle' => $entry->section->handle,
                'uri' => $entry->uri,
                'title' => $entry->title,
            );
        }
    }
}
