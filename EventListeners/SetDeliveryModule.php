<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace RetraitSelonAgenda\EventListeners;

use RetraitSelonAgenda\Classes\ICalCalendarManager;
use RetraitSelonAgenda\Model\OrderCalendarEvent;
use RetraitSelonAgenda\Model\OrderCalendarEventQuery;
use RetraitSelonAgenda\RetraitSelonAgenda;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\OrderAddressQuery;

class SetDeliveryModule implements EventSubscriberInterface
{
    /** @var  RequestStack */
    protected $requestStack;

    /** @var  ICalCalendarManager */
    protected $calendarClient;

    /**
     * SetDeliveryModule constructor.
     *
     * @param RequestStack $requestStack
     * @param ICalCalendarManager $calendarClient
     */
    public function __construct(RequestStack $requestStack, ICalCalendarManager $calendarClient)
    {
        $this->requestStack = $requestStack;
        $this->calendarClient = $calendarClient;
    }

    protected function isRetraitSelonAgenda($id)
    {
        return $id == RetraitSelonAgenda::getModuleId();
    }

    /**
     * @param OrderEvent $icalEvent
     */
    public function storeAdditionalDeliveryInformation(OrderEvent $event)
    {
        if ($this->isRetraitSelonAgenda($event->getDeliveryModule())) {
            $request = $this->requestStack->getCurrentRequest();

            $googleCalendarEventId = $request->get('selection');
            $intervalle = $request->get('intervalle', '1 month');

            // Retrouver l'ID et stocker les infos en base.
            $events = $this->calendarClient->getNextEvents($intervalle);

            foreach ($events as $icalEvent) {
                if ($icalEvent->uid == $googleCalendarEventId) {
                    // Found !
                    $request->getSession()->set(RetraitSelonAgenda::SELECTED_EVENT_SESSION_KEY, json_encode($icalEvent));

                    break;
                }
            }
        }
    }

    public function updateDeliveryAddress(OrderEvent $event)
    {
        if ($this->isRetraitSelonAgenda($event->getOrder()->getDeliveryModuleId())) {
            if (null !== $serializedIcalEvent = $this->requestStack
                    ->getCurrentRequest()
                    ->getSession()
                    ->get(RetraitSelonAgenda::SELECTED_EVENT_SESSION_KEY)
            ) {
                $orderId = $event->getOrder()->getId();

                if (null === $orderCalendarEvent = OrderCalendarEventQuery::create()->findOneByOrderId($orderId)) {
                    $orderCalendarEvent = new OrderCalendarEvent();
                }

                $orderCalendarEvent
                    ->setOrderId($orderId)
                    ->setSerializedEvent($serializedIcalEvent)
                    ->save()
                ;

                // RAZ de l'event en session
                $this->requestStack
                    ->getCurrentRequest()
                    ->getSession()
                    ->set(RetraitSelonAgenda::SELECTED_EVENT_SESSION_KEY, null)
                    ;
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('storeAdditionalDeliveryInformation', 10),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('updateDeliveryAddress', 256)
        );
    }
}
