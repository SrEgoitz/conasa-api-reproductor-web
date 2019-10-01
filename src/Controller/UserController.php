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
use App\Form\UserType;

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
     * 
     * @Route("/users", name="user_post", methods={"POST"})
     *
     */
    public function postUserAction(Request $request, UserService $userService, ValidatorInterface $validator)
    {

        $form = $this->createForm(UserType::class);
        $json = $this->getJson($request);
        $form->submit($json);

        $errors = $validator->validate($form);
        if (count($errors) > 0) {
            //$errorsString = (string) $errors;

            return new JsonResponse(['errors' => json_decode($this->container->get('jms_serializer')
            ->serialize($errors, 'json'))], JsonResponse::HTTP_BAD_REQUEST);
            
            //return new JsonResponse($errorsString, JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $userService->crearUsuario($request);
            
            return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_CREATED);
        
        }
    }

    /**
     * @Route("/user/{userId}", name="user_get",  methods={"GET"})
     */
    public function getUserAction(int $userId, UserService $userService)
    {

        try{
            $user = $userService->getUser($userId);
        } catch(\Exception $e) {
            
        }
       /*$repository = $this->getDoctrine()->getRepository(User::class);
       $user = $repository->findById($userId);*/
        

       return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($user, 'json')), Response::HTTP_OK);
    }

    /**
     * @Route("/user/{userId}", name="user_put", methods={"PUT"})
     */
    public function editUserAction(int $userId, Request $request){
        
        
        try{
            $user = $userService->getUser($userId);
        } catch(\Exception $e) {
                  
        }
        
       
        if ($newUser) {
            $newUser = $userService->changeUser($user, $repository);
        }
        return new JsonResponse(json_decode($this->container->get('jms_serializer')
            ->serialize($newUser, 'json')), Response::HTTP_OK);
    }


    /**
     * @Route("/user/{userId}", name="user_delete", methods={"DELETE"})
     */
    public function deleteUserAction(int $userId){
        try{
            $user = $userService->getUser($userId);
        } catch(\Exception $e) {
            
        }

        if($user){
            $repository->delete($user);
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
            throw new HttpException(400, 'Invalid json');
        }
        return $data;
    }
}
