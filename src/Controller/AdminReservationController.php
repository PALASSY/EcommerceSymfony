<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\AdminReservationType;
use Doctrine\Persistence\ObjectManager;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminReservationController extends AbstractController
{
    /**
     * Page qui affiche toutes les réservations
     * @Route("/admin/reservations", name="admin_reservations_list")
     */
    public function index(ReservationRepository $reporeservation): Response
    {
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reporeservation->findAll(),
        ]);
    }



    /**
     * Page qui modifie la réservation
     * @Route("/admin/reservation/{id}/edit", name="admin_reservation_edit")
     *
     * @param Reservation $reservation
     * @param ObjectManager $manager
     * @param Request $request
     * @return Response
     */
    public function edit(Reservation $reservation,ObjectManager $manager,Request $request)
    {
        $form = $this->createForm(AdminReservationType::class,$reservation);
        $form->handleRequest($request);
        $reservationId = $reservation->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($reservation);
            $manager->flush();

            $this->addFlash("success", "La réservation N°{$reservationId} a été modifié");

            return $this->redirectToRoute("admin_reservations_list");
        }

        return $this->render("admin/reservation/edit.html.twig", [
            "reservations"=> $reservation,
            "form"=>$form->createView()
        ]);
    }




    /**
     * Page qui supprime la réservation
     * @Route("/admin/reservation/{id}/delete", name="admin_reservation_delete")
     *
     * @param Reservation $reservation
     * @param ObjectManager $manager
     * @return void
     */
    public function delete(Reservation $reservation,ObjectManager $manager)
    {
        $reservationId = $reservation->getId();
        $manager->remove($reservation);
        $manager->flush();

        $this->addFlash("danger","La réservation N° <strong>{$reservationId}</strong> a été supprimé");

        return $this->redirectToRoute("admin_reservations_list");
    }
}
