<?php
namespace ICS\SsiBundle\Entity\Widgets;

use Doctrine\ORM\Mapping as ORM;
use ICS\DashboardBundle\Entity\Widget;
use Twig\Environment;

/**
 * @ORM\Table(name="logwidgets", schema="dashboard")
 * @ORM\Entity
 */
class LogWidget extends Widget
{
    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
        $this->setWidth(6);
        $this->setHeight(5);
    }

    public function getJs()
    {
        if (null == $this->twig) {
            return '';
        }

        return $this->twig->render('@Ssi/Widgets/LogWidget.js.twig', ['widget' => $this]);
    }

    public function getUI()
    {
        if (null == $this->twig) {
            return '';
        }

        return $this->twig->render('@Ssi/Widgets/LogWidget.html.twig', ['widget' => $this]);
    }
}