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
use RetraitSelonAgenda\Model\OrderCalendarEventQuery;
use RetraitSelonAgenda\RetraitSelonAgenda;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class DatesPossibles
 * @package RetraitSelonAgenda\Loop
 * @method getOrderId()
 */
class LieuDeLivraison extends BaseLoop implements ArraySearchLoopInterface
{
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('order_id')
        );
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        $rows = [];

        $serializedEvent = null;

        if (null !== $orderId = $this->getOrderId()) {
            if (null !== $calendarEvent = OrderCalendarEventQuery::create()->findOneByOrderId($orderId)) {
                $serializedEvent = $calendarEvent->getSerializedEvent();
            }
        } else {
            $serializedEvent = $this
                ->getCurrentRequest()
                ->getSession()
                ->get(RetraitSelonAgenda::SELECTED_EVENT_SESSION_KEY)
                ;
        }

        if (null !== $serializedEvent && null !== $icalEvent = json_decode($serializedEvent)) {
            $rows[] = [
                'id' => $icalEvent->uid,
                'debut' => $icalEvent->dtstart,
                'fin'   => $icalEvent->dtend,
                'chapo' => $icalEvent->summary,
                'description' => $icalEvent->description,
                'lieu' => $icalEvent->location
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
