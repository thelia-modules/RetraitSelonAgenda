<?php
/*************************************************************************************/
/*                                                                                   */
/*      This file is not free software                                               */
/*                                                                                   */
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 08/09/2017
 */
namespace RetraitSelonAgenda\Hook;

use RetraitSelonAgenda\Classes\GoogleCalendarClient;
use RetraitSelonAgenda\RetraitSelonAgenda;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleConfigQuery;

class HookManager extends BaseHook
{
    protected function getConfigVars()
    {
        $vars = [];

        // Passer toutes les variables de configuration au template
        if (null !== $params = ModuleConfigQuery::create()->findByModuleId(RetraitSelonAgenda::getModuleId())) {
            /** @var ModuleConfig $param */
            foreach ($params as $param) {
                $vars[ $param->getName() ] = $param->getValue();
            }
        }

        return $vars;

    }
    /**
     * @param HookRenderEvent $event
     */
    public function onModuleConfigure(HookRenderEvent $event)
    {
        $event->add(
            $this->render('retraitselonagenda/module-configuration.html', $this->getConfigVars())
        );
    }

    public function onOrderDeliveryExtra(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                "retraitselonagenda/choix-livraison.html",
                array_merge($event->getArguments(), $this->getConfigVars())
            )
        );
    }

    public function orderInvoiceDeliveryAddress(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                "retraitselonagenda/order-invoice-delivery-address.html",
                array_merge($event->getArguments(), $this->getConfigVars())
            )
        );
    }
}
