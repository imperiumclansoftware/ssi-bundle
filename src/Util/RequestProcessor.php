<?php

namespace ICS\SsiBundle\Util;

/**
 * File for RequestProcessor class
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class for create log data
 *
 * @package SsiBundle\Util
 */
class RequestProcessor
{
    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * RequestProcessor constructor.
     *
     * @param RequestStack $request
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    /**
     * Process log record
     *
     * @param array $record
     * @return array
     */
    public function processRecord(array $record)
    {
        $req = $this->request->getCurrentRequest();

        if($req!=null)
        {
            $record['extra']['client_ip']       = $req->getClientIp();
            $record['extra']['client_port']     = $req->getPort();
            $record['extra']['uri']             = $req->getUri();
            $record['extra']['query_string']    = $req->getQueryString();
            $record['extra']['method']          = $req->getMethod();
            $record['extra']['request']         = $req->request->all();
        }
        return $record;
    }
}
