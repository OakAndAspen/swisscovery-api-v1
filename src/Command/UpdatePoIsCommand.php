<?php

namespace App\Command;

use App\Entity\PointOfInterest;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePoIsCommand extends ContainerAwareCommand
{
    protected $exPOIs;

    protected function configure()
    {
        $this
            ->setName('app:update-pois')
            ->setDescription('Update all points of interest from the GeoAdmin API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->exPOIs = $this->getExistingFeatures($em);
        $offsettableUrls = [];

        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $percentage = $x * 10 + $y;
                $bbox = $this->calculateBBOX($x, $y);
                $url = $this->getIdentifyUrl($bbox);
                $content = file_get_contents($url);
                $features = json_decode($content, true)["results"];
                $message = 'X: ' . $x . ' | Y: ' . $y . ' (' . $percentage . ' %) | BBOX: ' . $bbox .
                    ' | ' . sizeof($features) . ' features';
                $output->writeln($message);
                if (sizeof($features) > 200) array_push($offsettableUrls, $url);

                foreach ($features as $f) {
                    $poi = $this->newFeature($f);
                    if ($poi) $em->persist($poi);
                }

                usleep(100000);
            }
        }

        $em->flush();

        $output->writeln("Done! " . sizeof($offsettableUrls) . " URLs contained more than 200 features.");
    }

    protected function getExistingFeatures(EntityManager $em)
    {
        $allPOIs = $em->getRepository(PointOfInterest::class)->findAll();
        $ids = [];

        foreach ($allPOIs as $poi) {
            array_push($ids, $poi->getFeatureId());
        }
        return $ids;
    }

    protected function newFeature($f)
    {
        $fid = $f['featureId'];
        if (in_array($fid, $this->exPOIs)) return null;
        else array_push($this->exPOIs, $fid);

        $att = $f['attributes'];

        $poi = new PointOfInterest();
        $poi->setFeatureId($fid);
        $poi->setX($f['geometry']['x']);
        $poi->setY($f['geometry']['y']);
        $poi->setLabel($att['label']);
        $poi->setCanton($att['kt_kz']);
        $poi->setCommune($att['gemeinde']);
        $poi->setWikiTitle($att['link_3_title']);
        $poi->setWikiLink($att['link_3_uri']);
        $poi->setCategory($att['kategorie']);
        return $poi;
    }

    protected function getIdentifyUrl($bbox)
    {
        return "https://api3.geo.admin.ch/rest/services/api/MapServer/identify?" .
            "lang=fr&" .
            "layers=all:ch.babs.kulturgueter&" .
            "geometryType=esriGeometryEnvelope&" .
            "geometry=" . $bbox . "&" .
            "mapExtent=" . $bbox . "&" .
            "imageDisplay=1000,1000,10&" .
            "tolerance=1";
    }

    protected function calculateBBOX($x, $y)
    {
        $xBase = 484000;
        $xIncr = 36000;
        $yBase = 72000;
        $yIncr = 23000;

        $minx = $xIncr * $x + $xBase;
        $maxx = $xIncr * ($x + 1) + $xBase;
        $miny = $yIncr * $y + $yBase;
        $maxy = $yIncr * ($y + 1) + $yBase;
        return $minx . ',' . $miny . ',' . $maxx . ',' . $maxy;
    }


    /* $this->findUrl = "https://api3.geo.admin.ch/rest/services/api/MapServer/find?" .
            "lang=fr&" .
            "layer=ch.babs.kulturgueter&" .
            "searchText=VD&" .
            "searchField=kt_kz";*/
}