<?php

/*
 * Ovverriding the profile controller of the FOSUserBundle
 */

namespace Tafrika\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Tafrika\UserBundle\Entity\User;

/**
 * Controller managing the user profile
 *
 * @author Ramy Rais <ramy.rais@gmail.com>
 */
class ProfileController extends Controller
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $page=1;
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $postPerPage = $this->container->getParameter('POST_PER_PAGE');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('TafrikaPostBundle:Post');
        $nsfw = $this->get('request')->getSession()->get('nsfw');
        $posts = $repository->findUsersPosts($user, $postPerPage, $page, $nsfw);
        $totalPost = $repository->countUserPosts($user, $nsfw);

        $votes = null;
        $matchingVotes = array();
        if($user != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findVoteByUserAndPosts($user, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }

        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'posts' => $posts,
            'page' => $page,
            'matchingVotes'=>$matchingVotes,
            'totalPage'=>ceil($totalPost/$postPerPage)
        ));
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
    public function showOtherUserProfileAction(User $user, $page){
        $postPerPage = $this->container->getParameter('POST_PER_PAGE');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('TafrikaPostBundle:Post');
        $nsfw = $this->get('request')->getSession()->get('nsfw');
        $posts = $repository->findUsersPosts($user, $postPerPage, $page, $nsfw);
        $totalPost = $repository->countUserPosts($user, $nsfw);

        $currentUser = $this->getUser();
        $votes = null;
        $matchingVotes = array();
        if($currentUser != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findVoteByUserAndPosts($currentUser, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }


        return $this->render('TafrikaUserBundle:Profile:showOtherUser.html.twig', array(
            'user' => $user,
            'posts' => $posts,
            'page' => $page,
            'matchingVotes'=>$matchingVotes,
            'totalPage'=>ceil($totalPost/$postPerPage)
        ));
    }

    /**
     * @return Response
     */
     public function addFollowedAction(){
         $request = $this->get('request');
         if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
             $request->getSession()->set('_security.main.target_path', $request->getUri());
             return $this->redirect($this->generateUrl('fos_user_security_login'));
         }
         if($request->isXmlHttpRequest()){
             $followed_id = $request->request->get('followed_id');
             $user = $this->get('security.context')->getToken()->getUser();
             $entityManager = $this->getDoctrine()->getManager();
             $repository = $entityManager->getRepository('TafrikaUserBundle:User');
             $followed = $repository->find($followed_id);
             if ($user->addfollowed($followed)){
                 $entityManager->persist($user);
                 $entityManager->persist($followed);
                 $entityManager->flush();
                 return new Response($followed->getUsername() ." is now followed");
             }
             return new Response($followed->getUsername() ." is not followed");
         }
     }

    /**
     * @return Response
     */
    public function deleteFollowedAction(){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if($request->isXmlHttpRequest()){
            $followed_id = $request->request->get('followed_id');
            $user = $this->get('security.context')->getToken()->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $entityManager->getRepository('TafrikaUserBundle:User');
            $followed = $repository->find($followed_id);
            if ($user->removefollowed($followed)){;
                $entityManager->persist($user);
                $entityManager->persist($followed);
                $entityManager->flush();
                return new Response($followed->getUsername() ." is not followed anymore");
            }
            return new Response($followed->getUsername() ." is still followed");
        }
    }
}
