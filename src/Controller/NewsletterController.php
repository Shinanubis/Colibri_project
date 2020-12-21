<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    public function newsletterForm()
    {
        $form = $this->createForm(NewsletterType::class);

        return $this->render("newsletter/_form.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function newsletterSubscribe(Request $request): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $email = $request->request->get("newsletter")["email"];
            $newsletter->setEmail($email);
            $em->persist($newsletter);
            $em->flush();
        }

        return $this->redirectToRoute('index');

    }
}
