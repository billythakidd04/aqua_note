<?php
/**
 * Created by PhpStorm.
 * User: thakidd
 * Date: 12/6/16
 * Time: 10:19 AM
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function homepageAction()
    {
        return $this->render('main/homepage.html.twig');
    }
}