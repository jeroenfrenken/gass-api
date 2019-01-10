<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 09/01/2019
 * Time: 13:28
 */

namespace App\EventSubscriber;

use App\Controller\Services\ActionRegisterService;
use App\Entity\UserToken;
use App\Interfaces\ApiAuthenticationInterface;
use App\Response\ApiResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EventSubscriber implements EventSubscriberInterface
{

    /** @var ContainerInterface $container */
    private $container;

    /**
     * Loads the container
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();

        if(!is_array($controller)) return;

        if ($controller[0] instanceof ApiAuthenticationInterface) {

            $request = $event->getRequest();

            // Always let options requests pass else there are problems in the front-end
            if ($request->isMethod('options')) {
                return $event->setController(function () {
                    return ApiResponse::okResponse();
                });
            }

            $token = $request->headers->get('authentication');

            if ($token === null) {
                return $event->setController(function () {
                    return ApiResponse::notAuthorized();
                });
            };

            $doctrine = $this->container->get('doctrine');

            // Get the user by token
            /** @var UserToken $user */
            $user = $doctrine->getRepository(UserToken::class)->findOneBy([
                'token' => $token
            ]);


            // not authorized as the user is not found
            if ($user === null) {

                return $event->setController(function () {
                    return ApiResponse::notAuthorized();
                });

            }

            /** @var ActionRegisterService $actionService */
            $actionService = $this->container->get('App\Controller\Services\ActionRegisterService');

            if (
            !$actionService->canDoAction($user->getUser()->getEmail(), ActionRegisterService::ACTION_OTHER)
            ) {

                return $event->setController(function () {
                    return ApiResponse::rateLimit();
                });

            }

            $actionService->registerAction($user->getUser()->getEmail(), ActionRegisterService::ACTION_OTHER);

            // set the user in the container for in controller usage

            $token = new UsernamePasswordToken($user->getUser(), null, 'main', []);

            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

        }

    }

    public function onKernelResponse(FilterResponseEvent $event)
    {

        // Set some headers to please the browser :)

        $responseHeaders = $event->getResponse()->headers;

        $responseHeaders->set('Access-Control-Allow-Origin', '*');
        $responseHeaders->set('Access-Control-Allow-Headers', "Access-Control-Allow-Methods, Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authentication");
        $responseHeaders->set('Access-Control-Allow-Credentials', 'true');
        $responseHeaders->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    }

    public static function getSubscribedEvents()
    {

        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse'
        );

    }

}