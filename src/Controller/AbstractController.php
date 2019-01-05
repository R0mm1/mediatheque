<?php
/**
 * Created by PhpStorm.
 * User: romain2
 * Date: 29/10/18
 * Time: 12:54
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends SymfonyController
{
    public function getParameters(Request $request)
    {
        $params = array_merge($request->query->all(), $request->request->all());

        if (in_array($request->getMethod(), [Request::METHOD_PUT, Request::METHOD_DELETE])) {
            $params += json_decode($request->getContent(), true);
        }

        return $params;
    }
}