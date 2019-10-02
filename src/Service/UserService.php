<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
  
  private $em;

  public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
    $this->em = $em;
    $this->encoder = $encoder;
  }


  public function validate(Request $request){
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

  public function crearUsuario(Request $request) : User{
    $usuario = new User();
    $variables = $request->request;
    $usuario->setNombre($variables->get('nombre'));
    $usuario->setApellidos($variables->get('apellidos'));
    $usuario->setEmail($variables->get('email'));
    $usuario->setUsername($variables->get('username'));
    $usuario->setFechaNacimiento(new \DateTime($variables->get('fecha_nacimiento')));
    $encodedPassword = $this->encoder->encodePassword($usuario, $variables->get('password'));
    $usuario->setPassword($encodedPassword);
    $this->em->persist($usuario);
    $this->em->flush();
    return $usuario;
  }

  public function getUser(int $userId) : User{
    $repository = $this->em->getRepository(User::class);
    $user = $repository->findOneById($userId);
    return $user;
  }


  public function changeUser(User $user, Request $request) : User{
    
    $variables = $request->request;
    $name = $variables->get('nombre');
    $surname = $variables->get('apellidos');
    $email = $variables->get('email');
    $fechaNa =$variables->get('fecha_nacimiento');
    $pass = $variables->get('password');

    if (!is_null($name) && $name != "") {
      $user->setNombre($name);
    }
    
    if (!is_null($surname) && $surname != "") {
      $user->setApellidos($surname);
    }

    if (!is_null($email) && $email != "") {
      $user->setEmail($email);
    }

    if (!is_null($fechaNa) && $fechaNa != "") {
      $user->setFechaNacimiento(new \DateTime($fechaNa));
    }

    if (!is_null($pass) && $pass != "") {
      $user->setPassword($pass);
    }

    $this->em->flush();

    return $user;
  }


  public function deleteUser(int $userId): User{
    $repository = $this->em->getRepository(User::class);
    $user = $repository->findOneById($userId);
    if ($user) {
      $this->em->remove($user);
      $this->em->flush();
    }
    return $user;
  }
  
  public function handleErrors(\Exception $e){
    $msg = $e->getMessage();
    $msg = mb_convert_encoding($msg, 'UTF-8', 'UTF-8');
    return $msg;
  }

  public function loginUser(string $email, string $pass){
    
    
    $encodePass = $this->encoder->encodePassword($user, pass);

    $repository = $this->em->getRepository(User::class);
    $user = $repository->findOneBy(['email' => $email]);

    if ($user) {
      if ($user->getPassword() == $encodePass) {
          return 200;
      }else {
        return 400;
      }
    }else {
      return 400;
    }


  }
}