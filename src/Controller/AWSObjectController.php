<?php

namespace App\Controller;

use App\Entity\AWSObject;
use App\Form\AWSObjectType;
use App\Repository\AWSObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/a/w/s/object")
 */
class AWSObjectController extends AbstractController
{
    /**
     * @Route("/", name="a_w_s_object_index", methods={"GET"})
     */
    public function index(AWSObjectRepository $aWSObjectRepository): Response
    {
        return $this->render('aws_object/index.html.twig', [
            'a_w_s_objects' => $aWSObjectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="a_w_s_object_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $aWSObject = new AWSObject();
        $form = $this->createForm(AWSObjectType::class, $aWSObject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($aWSObject);
            $entityManager->flush();

            return $this->redirectToRoute('a_w_s_object_index');
        }

        return $this->render('aws_object/new.html.twig', [
            'a_w_s_object' => $aWSObject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="a_w_s_object_show", methods={"GET"})
     */
    public function show(AWSObject $aWSObject): Response
    {
        return $this->render('aws_object/show.html.twig', [
            'a_w_s_object' => $aWSObject,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="a_w_s_object_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AWSObject $aWSObject): Response
    {
        $form = $this->createForm(AWSObjectType::class, $aWSObject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('a_w_s_object_index', [
                'id' => $aWSObject->getId(),
            ]);
        }

        return $this->render('aws_object/edit.html.twig', [
            'a_w_s_object' => $aWSObject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="a_w_s_object_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AWSObject $aWSObject): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aWSObject->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($aWSObject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('a_w_s_object_index');
    }
}
