<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace RetraitSelonAgenda;

use Propel\Runtime\Connection\ConnectionInterface;
use RetraitSelonAgenda\Model\OrderCalendarEventQuery;
use Thelia\Install\Database;
use Thelia\Model\Country;
use Thelia\Model\OrderPostage;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\Exception\DeliveryException;

class RetraitSelonAgenda extends AbstractDeliveryModule
{
    /** @var string */
    const DOMAIN_NAME = 'retraitselonagenda';

    const ICAL_URL = 'calendar_url';
    const NB_MAX_EVENTS = 'nb_max_evenements';
    const CACHE_LIFETIME_IN_MINUTES = 'cache_lifetime';

    const SELECTED_EVENT_SESSION_KEY = "retrait_selon_agenda.select_event";

    public function isValidDelivery(Country $country)
    {
        $icalUrl = trim(self::getConfigValue(self::ICAL_URL));

        return ! empty($icalUrl);
    }

    public function getPostage(Country $country)
    {
        return 0;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        try {
            OrderCalendarEventQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con);
            $database->insertSql(null, [ __DIR__ . '/Config/thelia.sql' ]);

            self::setConfigValue(self::CACHE_LIFETIME_IN_MINUTES, 30);
            self::setConfigValue(self::NB_MAX_EVENTS, 10);
        }
    }
}
