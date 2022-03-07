<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace RetraitSelonAgenda\Form;

use RetraitSelonAgenda\RetraitSelonAgenda;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

class ConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "calendar_url",
                "text",
                [
                    "constraints" => [ new NotBlank() ],
                    "label" => $this->translator->trans("Adresse URL privée de l'agenda", [], RetraitSelonAgenda::DOMAIN_NAME),
                    'label_attr'  => [
                        'help' => $this->translator->trans(
                            "Vous trouverez cette adresse dans les paramètres de votre agenda Google.",
                            [],
                            RetraitSelonAgenda::DOMAIN_NAME
                        ),
                    ],
                ]
            )
            ->add(
                "cache_lifetime",
                "number",
                [
                    "constraints" => [ new NotBlank(), new GreaterThan(0) ],
                    "label" => $this->translator->trans("Durée de vie du cache des données de l'agenda, en minutes ", [], RetraitSelonAgenda::DOMAIN_NAME),
                    'label_attr'  => [
                        'help' => $this->translator->trans(
                            "Délai en minutes pendant lequel Thelia va garder les informations de l'agenda Google en cache. Ceci permet de minimiser le nombre d'appels à Google Calendar",
                            [],
                            RetraitSelonAgenda::DOMAIN_NAME
                        ),
                    ],
                ]
            )
            ->add(
                "nb_max_evenements",
                "number",
                [
                    "constraints" => [ new NotBlank(), new GreaterThan(0) ],
                    "label" => $this->translator->trans("Nombre maximum d'événements à présenter ", [], RetraitSelonAgenda::DOMAIN_NAME),
                    'label_attr'  => [
                        'help' => $this->translator->trans(
                            "Il s'agit du nombre maximum de dates de livraison à présenter à vos clients.",
                            [],
                            RetraitSelonAgenda::DOMAIN_NAME
                        ),
                    ],
                ]
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "retraitselonagenda_configuration";
    }
}
