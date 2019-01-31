<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\PointOfInterest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/features/{canton}", name="serve-features", methods={"GET"})
     *
     * @param EntityManagerInterface $em
     * @param $canton
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function serveFeatures(Request $req, EntityManagerInterface $em, $canton)
    {
        $user = $req->get("user");
        $pois = $em->getRepository(PointOfInterest::class)->findBy([
            "canton" => strtoupper($canton)
        ]);

        $fColl = [
            "type" => "FeatureCollection",
            "features" => []
        ];

        foreach ($pois as $poi) {
            $comment = $em->getRepository(Comment::class)->findOneBy([
                "user" => $user,
                "pointOfInterest" => $poi
            ]);

            $feature = [
                "type" => "Feature",
                "id" => $poi->getId(),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [$poi->getX(), $poi->getY()]
                ],
                "properties" => [
                    "id" => $poi->getId(),
                    "featureId" => $poi->getFeatureId(),
                    "label" => $poi->getLabel(),
                    "canton" => $poi->getCanton(),
                    "commune" => $poi->getCommune(),
                    "wikiTitle" => $poi->getWikiTitle(),
                    "wikiLink" => $poi->getWikiLink(),
                    "category" => $poi->getCategory()
                ]
            ];

            if ($comment) {
                $feature['properties']['comment'] = [
                    "isInterested" => $comment->getIsInterested(),
                    "hasVisited" => $comment->getHasVisited(),
                    "text" => $comment->getText(),
                    "images" => $comment->getImages()
                ];
            } else {
                $feature['properties']['comment'] = [
                    "isInterested" => false,
                    "hasVisited" => false,
                    "text" => "",
                    "images" => []
                ];
            }

            array_push($fColl['features'], $feature);
        }

        return $this->json($fColl);
    }

    /**
     * @Route("/comment/{poiId}", name="comment", methods={"POST"})
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param $poiId
     * @return JsonResponse
     */
    public function comment(Request $req, EntityManagerInterface $em, $poiId)
    {
        $user = $req->get("user");
        $poi = $em->getRepository(PointOfInterest::class)->find($poiId);
        $comment = $em->getRepository(Comment::class)->findOneBy([
            "user" => $user,
            "pointOfInterest" => $poi
        ]);

        if (!$comment) {
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setPointOfInterest($poi);
            $comment->setIsInterested(false);
            $comment->setHasVisited(false);
            $comment->setText("");
            $comment->setImages([]);
        }


        if ($req->get("isInterested")) $comment->setIsInterested($req->get("isInterested"));
        if ($req->get("hasVisited")) $comment->setHasVisited($req->get("hasVisited"));
        if ($req->get("text")) $comment->setText($req->get("text"));

        $em->persist($comment);
        $em->flush();

        return new JsonResponse(["success" => "Comment was updated"]);
    }
}
