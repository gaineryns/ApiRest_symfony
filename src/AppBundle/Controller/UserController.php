<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\usersType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
class UserController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $users = $this->get("doctrine.orm.entity_manager")->getRepository('AppBundle:User')->findAll();
        return $users;
    }


    /**
     * @Rest\View()
     * @Rest\Get("/users/{user_id}")
     */
    public function getUserAction(Request $request){
        $user = $this->get("doctrine.orm.entity_manager")->getRepository("AppBundle:User")
            ->find($request->get('user_id'));

        if(empty($user)){
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/users")
     */
    public function postUserAction(Request $request){

        $user = new User();
        $form= $this->createForm(usersType::class, $user);

        $form->submit($request->request->all());

        if($form->isValid()){
            $em= $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        }else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{user_id}")
     */
    public function removeUserAction(Request $request){
        $user = $this->get("doctrine.orm.entity_manager")->getRepository("AppBundle:User")
            ->find($request->get('user_id'));

        $em= $this->get('doctrine.orm.entity_manager');
        $em->remove($user);
        $em->flush();
    }


    /**
     * @Rest\View()
     * @Rest\Put("/users/{user_id}")
     */
    public function updateUserAction(Request $request){
        $this->updateUser($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{user_id}")
     */
    public function patchUserAction(Request $request){
        $this->updateUser($request, false);
    }


    public function updateUser(Request $request, $clearMissing){
        $user = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:User')
            ->find($request->get('user_id'));

        if(empty($user)){
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(usersType::class, $user);

        $form->submit($request->request->all(), $clearMissing);
        if($form->isValid()){
            $em= $this->get('doctrine.orm.entity_manager');
            $em->merge($user);
            $em->flush();
            return $user;
        }else{
            return $form;
        }

    }
}
