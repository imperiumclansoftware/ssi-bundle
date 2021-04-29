<?php
namespace ICS\SsiBundle\Entity\Widgets;

/**
 * File for LogWidget configuration
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\Mapping as ORM;
use ICS\DashboardBundle\Entity\Widget;
use Twig\Environment;

/**
 * Widget for show log on dashboards
 *
 * @package SsiBundle\Entity\Widgets
 * @ORM\Table(name="logwidgets", schema="dashboard")
 * @ORM\Entity
 */
class LogWidget extends Widget
{
    /**
     * Class constructor
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
        $this->setWidth(6);
        $this->setHeight(5);
    }

    /**
     * {@inheritdoc}
     */
    public function getJs()
    {
        if (null == $this->twig) {
            return '';
        }

        return $this->twig->render('@Ssi/Widgets/LogWidget.js.twig', ['widget' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUI()
    {
        if (null == $this->twig) {
            return '';
        }

        return $this->twig->render('@Ssi/Widgets/LogWidget.html.twig', ['widget' => $this]);
    }
}