<?php

namespace ICS\SsiBundle\Controller;

use ICS\SsiBundle\Entity\Log;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LogController extends AbstractController
{
    /**
     * @Route("/log/list/{nbElements}/{page}" , name="ics_ssi_log_list")
     */
    public function index(Request $request, $nbElements=10, $page=0)
    {
        $counter=$this->getDoctrine()->getRepository(Log::class)->findBy([],['createdAt' => 'DESC']);
        $logs=$this->getDoctrine()->getRepository(Log::class)->findBy([],[
            'createdAt' => 'DESC'
        ],$nbElements,$page*$nbElements);


        return $this->render("@Ssi/admin/logsList.html.twig",[
            'logs' => $logs,
            'nbElements' => $nbElements
        ]);
    }
}
