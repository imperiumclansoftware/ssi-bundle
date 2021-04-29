<?php

namespace ICS\SsiBundle\Controller;

/**
 * File of controllers for logs
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use ICS\SsiBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controllers for logs
 *
 * @package SsiBundle\Controller
 */
class LogController extends AbstractController
{
    /**
     * Controller for widgets log list
     *
     * @return Response
     * @Route("/log/list/{nbElements}/{page}" , name="ics_ssi_log_list")
     */
    public function index(Request $request, $nbElements=10, $page=0)
    {
        $logs=$this->getDoctrine()->getRepository(Log::class)->findBy([],[
            'createdAt' => 'DESC'
        ],$nbElements,$page*$nbElements);


        return $this->render("@Ssi/admin/logsList.html.twig",[
            'logs' => $logs,
            'nbElements' => $nbElements
        ]);
    }
}
