<?php

namespace Dsnet\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dsnet_user_homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/connect.html", name="dsnet_user_connect")
     */
    public function connectAction()
    {
        return $this->redirect($this->get('dsnet_user.panel_resource_owner')->getAuthorizationUrl());
    }
}
