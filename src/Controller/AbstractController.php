<?php
/**
 * Created by PhpStorm.
 * User: romain2
 * Date: 29/10/18
 * Time: 12:54
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends SymfonyAbstractController
{
    public function getParameters(Request $request)
    {
        return array_merge($request->query->all(), $request->request->all());
    }
}