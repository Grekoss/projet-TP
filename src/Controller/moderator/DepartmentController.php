<?php

namespace App\Controller\moderator;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DepartmentController extends Controller
{
    /**
     * @Route("/moderator/department/list", name="moderator_department_list", methods="GET")
     */
    public function index(DepartmentRepository $departmentRepository): Response
    {
        return $this->render('moderator/department/index.html.twig', ['departments' => $departmentRepository->orderByDepartmentNumber()]);
    }

    /**
     * @Route("/moderator/department//new", name="moderator_department_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            return $this->redirectToRoute('moderator_department_list');
        }

        return $this->render('moderator/department/new.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/moderator/department/{id}", name="moderator_department_show", methods="GET")
     */
    public function show(Department $department, EventRepository $eventRepository): Response
    {
        return $this->render('moderator/department/show.html.twig', [
            'department' => $department,
            'events' => $eventRepository->findBy(['department' => $department]),
        ]);
    }

    /**
     * @Route("/moderator/department/{id}/edit", name="moderator_department_edit", methods="GET|POST")
     */
    public function edit(Request $request, Department $department): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_department_edit', ['id' => $department->getId()]);
        }

        return $this->render('moderator/department/edit.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("moderator/department/{id}/delete", name="moderator_department_delete", methods="DELETE")
     */
    public function delete(Request $request, Department $department): Response
    {
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($department);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_department_list');
    }
}
