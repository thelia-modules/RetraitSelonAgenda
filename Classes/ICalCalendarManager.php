<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 13/09/2017 10:44
 */
namespace RetraitSelonAgenda\Classes;

use Doctrine\Common\Cache\FilesystemCache;
use ICal\Event;
use ICal\ICal;
use RetraitSelonAgenda\RetraitSelonAgenda;
use Thelia\Log\Tlog;

class ICalCalendarManager
{
    /** @var FilesystemCache */
    protected $cacheAdapter;

    /** @var ICal */
    protected $ical;

    /**
     * ICalCalendarManager constructor.
     */
    public function __construct()
    {
        $this->ical = new ICal(false, array(
            'defaultWeekStart'      => 'MO',  // Default value
            'skipRecurrence'        => false, // Default value
            'useTimeZoneWithRRules' => false, // Default value
            'defaultTimeZone'       => "Europe/Paris"
        ));

        $this->cacheAdapter = new FilesystemCache(THELIA_CACHE_DIR . 'calendar_maganer');

        $this->ical->initString($this->getCalendarData());
    }


    public function clearCache()
    {
        $this->cacheAdapter->deleteAll();
    }

    /**
     * @param string $interval
     * @return Event[]
     * @throws \Exception
     */
    public function getNextEvents($interval)
    {
        $key = 'events_ '. $interval;

        if (false === $data = $this->cacheAdapter->fetch($key)) {
            $data = $this->ical->eventsFromInterval($interval);

            $this->cacheAdapter->save(
                $key,
                $data,
                60 * RetraitSelonAgenda::getConfigValue(RetraitSelonAgenda::CACHE_LIFETIME_IN_MINUTES)
            );
        }

        return $data;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getCalendarData()
    {
        $filename = RetraitSelonAgenda::getConfigValue(RetraitSelonAgenda::ICAL_URL);

        $key = 'google_ical_data_' . md5($filename);

        if (false === $data = $this->cacheAdapter->fetch($key)) {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );

            if (false === $data = @file_get_contents($filename, false, stream_context_create($arrContextOptions))) {
                Tlog::getInstance()->err("Cannot read file path or URL '{$filename}'.");

                return '';
            }

            // Limiter la taille des données à traiter
            // $data = substr($data, 0, 5000);
            $this->cacheAdapter->save(
                $key,
                $data,
                60 * RetraitSelonAgenda::getConfigValue(RetraitSelonAgenda::CACHE_LIFETIME_IN_MINUTES)
            );
        }

        return $data;
    }
}
