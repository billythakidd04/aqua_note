<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Service\MarkdownTransformer;
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

        $genusNote = new GenusNote();
        $genusNote->setUsername('Aquaweaver');
        $genusNote->setUserAvatarFilename('ryan.jpg');
        $genusNote->setNote('I counted 8 legs... as they were wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();

        $em->persist($genus);
        $em->persist($genusNote);
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
            ->findAllPublishedOrderedByRecentlyActive();

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
        /** @var  $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Genus $genus */
        $genus = $em->getRepository("AppBundle:Genus")
            ->findOneBy(['name' => $genusName]);

        if(!$genus)
        {
            $message = "Genus not found: $genusName";
            $this->get('logger')->error($message);
            throw $this->createNotFoundException($message);
        }

        $transformer = $this->get('app.markdown_transformer');
        $funFact = $transformer->parse($genus->getFunFact());

        $this->get('logger')->info('Showing genus: '.$genusName);

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' => count($recentNotes),
        ));
    }

    /**
     * @param $genus
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getNotesAction(Genus $genus)
    {
        foreach($genus->getNotes() as $note)
        {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y'),
            ];
        }

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse($data);
    }
}
