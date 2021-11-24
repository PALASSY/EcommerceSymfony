<?php

namespace App\Controller;

use App\Entity\Table;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\TableRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    /**
     * Page de réservation de table par rapport à l'ADN(slug) de food
     * @Route("/tables/{id}/reservation", name="tables_reservation")
     * @param [type] $id
     * @param Table $table    
     */
    public function reservation($id, Request $request, ObjectManager $manager, Table $table): Response

    {
        //On va créer un nouvel Objet Reservation()
        $reservation = new Reservation();

        //dd($table->getNotAvailableDays());

        //On va créer le formulaire 
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        //si soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On va récupérer la personne qui est connecté
            $client = $this->getUser();

            //On va setter l'Objet Reservation 
            $reservation->setClient($client)
                ->setTabledisponible($table);
            //Il manque la date qu'on va créer automatiquement dans l'Entity Reservation()

            //Envoi message d'erreur si la date n'est plus disponible avant d'enregister
            if (!$reservation->isBookabledays()) {
                $this->addFlash("danger", "Date non disponible,choisissez autre date");
            } else {

                //Enregister la reservation
                $manager->persist($reservation);
                //dd($reservation->getDays());
                $manager->flush();

                //Faire une redirection
                //Et afficher dans cette page de rédirection un message de sucess, pour cela il va falloir une variable [alert => true]
                return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId(), 'alert' => true]);
            }
        }

        return $this->render('reservation/reservation.html.twig', [
            'table' => $table,
            'form' => $form->createView()
        ]);
    }




    /**
     * Page résumé de la réservation 
     * @Route("/reservation/{id}", name="reservation_show")
     *
     * @param Reservation $reservation
     * @return 
     */
    public function showResevation(Reservation $reservation): Response
    {
        return $this->render("reservation/show.html.twig", ['reservation' => $reservation]);
    }
}
