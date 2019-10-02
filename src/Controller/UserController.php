<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use App\Service\UserService;
use App\Service\HelperService;
use App\Form\UserType;
use App\Form\LoginType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="user_api")
 */
class UserController extends FOSRestController
{
    /**
     * 
     * @Get("/", name="homepage")
     *
     */
    public function homePageAction(Request $request)
    {
        return new JsonResponse(
            [
                'message' => 'Bienvenido al api rest de Egoitz',
            ],
            JsonResponse::HTTP_OK
        );    
    }

    /**
     * @Route("/users", name="user_post", methods={"POST"})
     */
    public function postUserAction(Request $request, UserService $userService, ValidatorInterface $validator, HelperService $helperService)
    {

        $form = $this->createForm(UserType::class);
        $json = $this->getJson($request);
        $form->submit($json);

        $errors = $validator->validate($form);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => json_decode($this->container->get('jms_serializer')
            ->serialize($errors, 'json'))], JsonResponse::HTTP_BAD_REQUEST);
        } 

        if ($form->isSubmitted() && $form->isValid()) {
            
            try{
                $user = $userService->crearUsuario($request);
            } catch(\Exception $e) {
                $msg = $helperService->handleErrors($e);
                return new JsonResponse(['error' => $msg], Response::HTTP_BAD_REQUEST);
            } 
            
            return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_CREATED);        
        }
    }
    /**
     * @Route("/login", name="user_login", methods={"POST"})
     */
    public function loginUserAction(Request $request, UserPasswordEncoderInterface $encoder){
        
        $variables = $request->request;
        $email = $variables->get('email');
        $password = $variables->get('password');
        $error = false;

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $email]);
        if(!$user) $error = true;


        $validPassword = $encoder->isPasswordValid(
            $user, // the encoded password
            $password,       // the submitted password
        );

        if(!$validPassword) $error = true;

        if($error){
            return new JsonResponse(['error' => "El Email o el password introducido no es correcto"], Response::HTTP_BAD_REQUEST);
        }
        $now = new \DateTime();
        $now->modify("+ 2 days");
        return new JsonResponse(['message' => $now->format("d-m-Y")], Response::HTTP_OK);

    }

    /**
     * @Route("/users/{userId}", name="user_get",  methods={"GET"})
     */
    public function getUserAction(int $userId, UserService $userService, HelperService $helperService){

        try{
            $user = $userService->getUser($userId);
        } catch(\Exception $e) {
            $msg = $helperService->handleErrors($e);
            return new JsonResponse(['error' => $msg], Response::HTTP_BAD_REQUEST);
        }        

       return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_OK);
    }

    /**
     * @Route("/users/{userId}", name="user_put", methods={"PUT"})
     */
    public function editUserAction(int $userId, Request $request, UserService $userService, HelperService $helperService){
               
        try{
            $user = $userService->getUser($userId);
            if ($user) {
                $user = $userService->changeUser($user, $request);
            }
        } catch(\Exception $e) {
            $msg = $helperService->handleErrors($e);
            return new JsonResponse(['error' => $msg], Response::HTTP_BAD_REQUEST);   
        }
        return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_OK);
    }


    /**
     * @Route("/users/{userId}", name="user_delete", methods={"DELETE"})
     */
    public function deleteUserAction(int $userId, UserService $userService, HelperService $helperService){
        try{
            $user = $userService->deleteUser($userId);
        } catch(\Exception $e) {
            $msg = $helperService->handleErrors($e);
            return new JsonResponse(['error' => $msg], Response::HTTP_BAD_REQUEST);
        }

            return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_OK);
    }

    




    /**
     * @param Request $request
     *
     * @return mixed
     *
     * @throws HttpException
     */
    private function getJson(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(["error" => "Formato inválido json"], Response::HTTP_OK);
        }
        return $data;
    }
}