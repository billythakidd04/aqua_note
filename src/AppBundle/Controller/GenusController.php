<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));

        $em = $this->getDoctrine()->getManager();

        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Genus Created!</body></html>');
    }

    /**
     * @Route("/genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllOrderedBySize();

        return $this->render('genus/list.html.twig', [
                'genuses' => $genuses,
            ]);
    }

    /**
     * @param $genusName
     * @Route("/genus/{genusName}", name="genus_show")
     * @return Response
     */
    public function showAction($genusName)
    {
        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository("AppBundle:Genus")->findOneBy(['name' => $genusName]);

        if(!$genus)
        {
            $message = "Genus not found: $genusName";
            $this->get('logger')->error($message);
            throw $this->createNotFoundException($message);
        }

        /*$cache = $this->get('doctrine_cache.providers.my_markdown_cache');
        $key = md5($funFact);

        if($cache->contains($key))
        {
            $funFact = $cache->fetch($key);
        }
        else
        {
            sleep(1);
            $funFact = $this->get('markdown.parser')->transform($funFact);
            $cache->save($key,$funFact);
        }*/

        $this->get('logger')->info('Showing genus: '.$genusName);

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
        ));
    }

    /**
     * @param $genusName
     * @Route("/genus/{genusName}/notes", name="genus_show_notes")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getNotesAction($genusName)
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];
        $data = [
            'notes' => $notes,
            'genusName' => $genusName,
        ];

        return new JsonResponse($data);
    }
}
