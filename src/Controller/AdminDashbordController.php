<?php

namespace App\Controller;

use App\Service\Statistic\Statistics;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminDashbordController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashbord")
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas l'authorisation de renter dans Admin")
     */
    public function index(ObjectManager $manager, Statistics $statsService): Response
    {
        $stats = $statsService->getStatistics();

        $clefs = [];
        $datas = [];
        foreach ($stats as $key => $value) {
            $datas[] += $value;
            $clefs[] .= $key;
        }
        $data = new JsonResponse($datas);
        $content = $data->getContent();
        
        $clefData = new JsonResponse($clefs);
        $contentClef = $clefData->getContent();
        //dd($contentClef);


        $bestFoods = $statsService->getFoodStats('DESC');
            //dd($bestFoods);

        $worstFoods = $statsService->getFoodStats('ASC');

        return $this->render('admin/dashbord/index.html.twig', [
            'stats' => $stats,
            'bestFoods' => $bestFoods,
            'worstFoods' => $worstFoods,
            'content' => $content,
            'contentClef' =>$contentClef
        ]);
    }
}
