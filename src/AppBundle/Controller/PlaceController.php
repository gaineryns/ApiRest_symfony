<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Places;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
{

    /**
     * @Route("/places", name="places_list")
     * @Method({"GET"})
     */
    public function getPlacesAction(Request $request)
    {
        $places= $this-> get('doctrine.orm.entity_manager')->getRepository('AppBundle:Places')->findAll();

        /* @var $places Place[] */
        $formatted = [];
        foreach ($places as $place) {
            $formatted[] = [
                'id' => $place->getId(),
                'name' => $place->getName(),
                'address' => $place->getAddress(),
            ];
        }

        return new JsonResponse([$formatted]);
    }

    /**
     * @Route("/places/{place_id}",requirements={"place_id" = "\d+"}, name="places_one")
     * @Method({"get"})
     */
    public function getPlaceAction(Request $request){
        $place = $this->get("doctrine.orm.entity_manager")->getRepository("AppBundle:Places")
            ->find($request->get('place_id'));

        if(empty($place)){
            return new JsonResponse(['message' => 'place not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $place->getId(),
            'name' => $place->getName(),
            'address' => $place->getAddress(),
        ];
        return new JsonResponse($formatted);
    }
}