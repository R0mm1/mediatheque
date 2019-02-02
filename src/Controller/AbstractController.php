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

        $requestContent = json_decode($request->getContent(), true);
        if (json_last_error() == JSON_ERROR_NONE) {
            $params = array_merge($params, $requestContent);
        }

        return $params;
    }
}