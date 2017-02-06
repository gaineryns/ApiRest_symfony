<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Places;
use AppBundle\Form\Type\placeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/places")
     */
    public function getPlacesAction(Request $request)
    {
        $places= $this-> get('doctrine.orm.entity_manager')->getRepository('AppBundle:Places')->findAll();
        return $places;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}")
     */

    public function getPlaceAction(Request $request){
        $place = $this->get("doctrine.orm.entity_manager")->getRepository("AppBundle:Places")
            ->find($request->get('id'));

        if(empty($place)){
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }
        return $place;
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/places")
     */

    public function postPlaceAction(Request $request){

        $place = new Places();
        $form = $this->createForm(placeType::class, $place);

        $form->submit($request->request->all());
        if($form->isValid()){
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($place);
            $em->flush();
            return $place;
        }else { return $form;}
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/places/{id}")
     */
    public function removePlaceAction(Request $request){
        $em = $this->get('doctrine.orm.entity_manager');
        $place = $em->getRepository('AppBundle:Places')->find($request->get('id'));

        $em->remove($place);
        $em->flush();
    }


    /**
     * @Rest\View()
     * @Rest\Put("/places/{id}")
     */
    public function updatePlaceAction(Request $request){

        $this->updatePlace($request, true);

    }


    /**
     * @Rest\View()
     * @Rest\Patch("/places/{id}")
     */
    public function patchPlaceAction(Request $request){

        $this->updatePlace($request, false);

    }


    private function updatePlace(Request $request, $clearMissing)
    {
        $place = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Places')
            ->find($request->get('id'));

        if (empty($place)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(placeType::class, $place);

        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }
}
