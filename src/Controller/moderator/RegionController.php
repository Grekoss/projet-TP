<?php

namespace App\Controller\moderator;

use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\DepartmentRepository;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RegionController extends Controller
{
    /**
     * @Route("/moderator/region/list", name="moderator_region_list", methods="GET")
     */
    public function index(RegionRepository $regionRepository): Response
    {
        return $this->render('moderator/region/index.html.twig', ['regions' => $regionRepository->findAll()]);
    }

    /**
     * @Route("/moderator/region/new", name="moderator_region_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            return $this->redirectToRoute('moderator_region_list');
        }

        return $this->render('moderator/region/new.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/moderator/region/{id}", name="moderator_region_show", methods="GET")
     */
    public function show(Region $region, DepartmentRepository $departmentRepository): Response
    {
        return $this->render('moderator/region/show.html.twig', [
            'region' => $region,
            'departments' => $departmentRepository->findBy(['region' => $region]),
        ]);
    }

    /**
     * @Route("/moderator/region/{id}/edit", name="moderator_region_edit", methods="GET|POST")
     */
    public function edit(Request $request, Region $region): Response
    {
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_region_edit', ['id' => $region->getId()]);
        }

        return $this->render('moderator/region/edit.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/moderator/region/{id}", name="moderator_region_delete", methods="DELETE")
     */
    public function delete(Request $request, Region $region): Response
    {
        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($region);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_region_index');
    }
}
