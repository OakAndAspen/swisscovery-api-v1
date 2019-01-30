<?php

namespace App\Controller;

use App\Entity\PointOfInterest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/features/{canton}", name="main")
     *
     * @param EntityManagerInterface $em
     * @param $canton
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serveFeatures(EntityManagerInterface $em, $canton)
    {
        $features = $em->getRepository(PointOfInterest::class)->findBy([
            "canton" => strtoupper($canton)
        ]);

        $fColl = [
            "type" => "FeatureCollection",
            "features" => []
        ];

        foreach ($features as $f) {
            array_push($fColl['features'], [
                "type" => "Feature",
                "id" => $f->getId(),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [$f->getX(), $f->getY()]
                ],
                "properties" => [
                    "id" => $f->getId(),
                    "featureId" => $f->getFeatureId(),
                    "label" => $f->getLabel(),
                    "canton" => $f->getCanton(),
                    "commune" => $f->getCommune(),
                    "wikiTitle" => $f->getWikiTitle(),
                    "wikiLink" => $f->getWikiLink(),
                    "category" => $f->getCategory()
                ]
            ]);
        }

        return $this->json($fColl);
    }
}
