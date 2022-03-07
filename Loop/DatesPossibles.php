<?php
/*************************************************************************************/
/*                                                                                   */
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*                                                                                   */
/*************************************************************************************/

namespace RetraitSelonAgenda\Loop;

use ICal\Event;
use RetraitSelonAgenda\Classes\ICalCalendarManager;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class DatesPossibles
 * @package RetraitSelonAgenda\Loop
 * @method getIntervalle()
 */
class DatesPossibles extends BaseLoop implements ArraySearchLoopInterface
{
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('intervalle', '1 month')
        );
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        $rows = [];

        /** @var ICalCalendarManager $calendarClient */
        $calendarClient = $this->container->get("retraitselonagenda.ical_manager");

        $events = $calendarClient->getNextEvents($this->getIntervalle());

        $rows = [];

        /** @var Event $event */
        foreach ($events as $event) {
            $rows[] = [
                'id' => $event->uid,
                'debut' => $event->dtstart,
                'fin' => $event->dtend,
                'chapo' => $event->summary,
                'description' => $event->description,
                'lieu' => $event->location
            ];
        }

        return $rows;
    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $row) {
            $loopResultRow = new LoopResultRow();

            foreach ($row as $name => $value) {
                $loopResultRow->set(strtoupper($name), $value);
            }

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
