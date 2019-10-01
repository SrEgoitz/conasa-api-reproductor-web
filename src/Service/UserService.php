<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserService
{
  
  private $em;

  public function __construct(EntityManagerInterface $em){
    $this->em = $em;
  }


  public function validate(Request $request)
  {
    $mensaje = [];
    $nombre = $request->request->get('nombre');
    $apellidos = $request->request->get('apellidos');
    $email = $request->request->get('email');
    $fecha_nacimiento = $request->request->get('fecha_nacimiento');

    if ($name == "") {
      $mensaje[] = "El campo nombre es obligatorio";
    }
    else {
      if (strlen($nombre) > 15)
      {
        $mensaje[] = "La longitud máxima del campo nombre no debe exceder los 15 caracteres";
      }
    }

    if ($apellidos == "")
    {
      $mensaje[] = "El campo apellidos es obligatorio";
    }
    else
    {
      if (strlen($apellidos) > 40)
      {
        $mensaje[] = "La longitud máxima del campo apellidos no debe exceder los 40 caracteres";
      }
      if (str_word_count($apellidos, 0) != 2)
      {
        $mensaje[] = "El campo apellidos debe contener exactamente dos apellidos, separados por un espacio. 
        En caso de compuesto debe ir todo junto";
      }
    }

    if ($email == "")
    {
      $mensaje[] = "El campo email es obligatorio";
    }
    else
    {
      if (filter_var($email, FILTER_VALIDATE_EMAIL) === false  )
      {
        $mensaje[]= "El correo electronico introducido no es correcto";
      }
    }


    if ($fecha_nacimiento == "")
    {
      $mensaje[] = "El campo fecha de nacimiento es obligatorio";
    }

    return $mensaje;
  }

  public function crearUsuario(Request $request) : User
  {
    $usuario = new User();
    $variables = $request->request;
    $usuario->setNombre($variables->get('nombre'));
    $usuario->setApellidos($variables->get('apellidos'));
    $usuario->setEmail($variables->get('email'));
    $usuario->setFechaNacimiento(new \DateTime($variables->get('fecha_nacimiento')));
    $usuario->setPassword($variables->get('password'));
    $this->em->persist($usuario);
    $this->em->flush();
    return $usuario;
  }

  public function getUser(int $userId) : User
  {
    if (!is_int($userId)) {
      return null;
    }
    $repository = $this->em->getRepository(User::class);
    $user = $repository->findOneById($userId);
    return $user;
    //return $this->repository->findById($userId);
    /* $repository = $this->getDoctrine()->getRepository(User::class);
    $user = $repository->findById($userId);
 */
  }


  public function changeUser(User $user, Request $request) : User
  {

    $newUser = new User();
    $name = $variables->get('nombre');
    $surname = $variables->get('apellidos');
    $email = $variables->get('email');
    $fechaNa =$variables->get('fecha_nacimiento');
    $pass = $variables->get('password');

    if (is_null($name) || $name == "") {
      $newUser->setNombre($user->getNombre());
    }else{
      $newUser->setNombre($name);
    }
    
    if (is_null($surname) || $surname == "") {
      $newUser->setApellidos($user->getApellidos());
    }else{
      $newUser->setApellidos($surname);
    }

    if (is_null($email) || $email == "") {
      $newUser->setEmail($user->getEmail());
    }else{
      $newUser->setEmail($name);
    }

    if (is_null($fechaNa) || $fechaNa == "") {
      $newUser->setFechaNacimiento($user->getFechaNacimiento());
    }else{
      $newUser->setFechaNacimiento(new \DateTime($fechaNa));
    }

    if (is_null($pass) || $pass == "") {
      $newUser->setPassword($user->getPassword());
    }else{
      $newUser->setPassword($pass);
    }

    $this->em->flush();
    return $newUser;
  }

  
}