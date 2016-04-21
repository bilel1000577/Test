<?php


namespace Quizmoo\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleProvider implements UserProviderInterface
{
  /**
   * @var \GoogleApi
   */
  protected $googleApi;
  protected $userManager;
  protected $validator;
  protected $em;

  public function __construct( $googleApi, $userManager, $validator, $em )
  {
    $this->googleApi = $googleApi;
    $this->userManager = $userManager;
    $this->validator = $validator;
    $this->em = $em;
  }

  public function supportsClass( $class )
  {
    return $this->userManager->supportsClass( $class );
  }

  public function findUserByGIdOrEmail( $gId, $email = null )
  {
    $user = $this->userManager->findUserByUsernameOrEmail( $email );
    if ( !$user )
      $user = $this->userManager->findUserBy( array( 'googleID' => $gId ) );
    return $user;
  }

  public function loadUserByUsername( $username )
  {
    try
    {
      $gData = $this->googleApi->getOAuth( )->userinfo->get( );
    }
    catch ( \Exception $e )
    {
      $gData = null;
    }

    $user = $this->findUserByGIdOrEmail( $username, isset( $gData['email'] ) ? $gData['email'] : null );

    if ( !empty( $gData ) )
    {
      if ( empty( $user ) )
      {
        $user = $this->userManager->createUser( );
        $user->setEnabled( true );
        $user->setPassword( '' );
      //  $user->setSalt( '' );
      }

      if ( isset( $gData['id'] ) )
      {
          $user->setGoogleID( $gData['id'] );
      }
      if ( isset( $gData['name'] ) )
      {
        $nameAndLastNames = explode( " ", $gData['name'] );
        if ( count( $nameAndLastNames ) > 1 )
        {
          $user->setFirstname( $nameAndLastNames[0] );
          $user->setLastname( $nameAndLastNames[1] );
         // $user->setLastname2( ( count( $nameAndLastNames ) > 2 ) ? $nameAndLastNames[2] : "" );
        }
        else
        {
          $user->setFirstname( $nameAndLastNames[0] );
          $user->setLastname( "" );
          $user->setLastname2( "" );
        }
      }
      if ( isset( $gData['email'] ) )
      {
        $user->setEmail( $gData['email'] );
        $user->setUsername( $gData['email'] );
      }
      else
      {
        $user->setEmail( $gData['id'] . "@google.com" );
        $user->setUsername( $gData['id'] . "@google.com" );
      }

      if ( count( $this->validator->validate( $user, 'Google' ) ) )
      {
        // TODO: the user was found obviously, but doesnt match our expectations, do something smart
        throw new UsernameNotFoundException( 'The google user could not be stored');
      }
      $this->userManager->updateUser( $user );
    }

    if ( empty( $user ) )
    {
      throw new UsernameNotFoundException( 'The user is not authenticated on google');
    }

    return $user;
  }

  public function refreshUser( UserInterface $user )
  {
    if ( !$this->supportsClass( get_class( $user ) ) || !$user->getGoogleId( ) )
    {
      throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', get_class( $user ) ));
    }

    return $this->loadUserByUsername( $user->getGoogleId( ) );
  }
}