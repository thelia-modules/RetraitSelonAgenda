<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia 2 RetraitSelonAgenda payment module                                               */
/*                                                                                   */
/*      Copyright (c) CQFDev                                                         */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*************************************************************************************/

namespace RetraitSelonAgenda\Controller;

use RetraitSelonAgenda\RetraitSelonAgenda;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

/**
 * RetraitSelonAgenda payment module
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class ConfigurationController extends BaseAdminController
{
    public function clearCache()
    {
        $this->getContainer()->get("retraitselonagenda.ical_manager")->clearCache();

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/RetraitSelonAgenda'));
    }

    public function configure()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'RetraitSelonAgenda', AccessManager::UPDATE)) {
            return $response;
        }

        $configurationForm = $this->createForm('retraitselonagenda.configuration.form');

        try {
            $form = $this->validateForm($configurationForm, "POST");

            // Get the form field values
            $data = $form->getData();

            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    $value = implode(';', $value);
                }

                RetraitSelonAgenda::setConfigValue($name, $value);
            }

            $this->adminLogAppend(
                "retraitselonagenda.configuration.message",
                AccessManager::UPDATE,
                sprintf("RetraitSelonAgenda configuration updated")
            );

            if ($this->getRequest()->get('save_mode') == 'stay') {
                // If we have to stay on the same page, redisplay the configuration page/
                $url = '/admin/module/RetraitSelonAgenda';
            } else {
                // If we have to close the page, go back to the module back-office page.
                $url = '/admin/modules';
            }

            $this->getContainer()->get("retraitselonagenda.ical_manager")->clearCache();

            return $this->generateRedirect(URL::getInstance()->absoluteUrl($url));
        } catch (FormValidationException $ex) {
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error_msg = $ex->getMessage();
        }

        $this->setupFormErrorContext(
            $this->getTranslator()->trans("RetraitSelonAgenda configuration", [], RetraitSelonAgenda::DOMAIN_NAME),
            $error_msg,
            $configurationForm,
            $ex
        );

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/RetraitSelonAgenda'));
    }
}
