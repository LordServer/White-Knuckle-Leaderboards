<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use App\Provider\ExtendedDiscordResourceOwner;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
final class DiscordAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('auth_discord_start'), Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return 'auth_discord_login' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient('discord');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var ExtendedDiscordResourceOwner $discordUser */
                $discordUser = $client->fetchUserFromToken($accessToken);

                $user = $this->userRepository->findOneBy(['discord_id' => $discordUser->getId()]);
                $discord_roles = $discordUser->getDiscordRoles();

                if (null === $user) {
                    $user = new User();
                    $user->setUsername($discordUser->getGlobalName());
                    $user->setDiscordId($discordUser->getId());
                    $user->setAvatar($discordUser->getAvatarHash());

                    // Setup initial Roles
                    if (in_array($_ENV['DISCORD_ADMIN_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_DISCORD_ADMIN');
                    } else {
                        $user->removeRole('ROLE_DISCORD_ADMIN');
                    }
                    if (in_array($_ENV['DISCORD_MODERATOR_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_DISCORD_MODERATOR');
                    } else {
                        $user->removeRole('ROLE_DISCORD_MODERATOR');
                    }
                    if (in_array($_ENV['DISCORD_AUTHORIZER_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_DISCORD_AUTHORIZER');
                    } else {
                        $user->removeRole('ROLE_DISCORD_AUTHORIZER');
                    }

                    // DateTime Tracking
                    $user->setCreated(new \DateTimeImmutable());
                    $user->setModified(new \DateTime());

                    $this->entityManager->persist($user);
                } else {
                    if ($discordUser->getAvatarHash() !== $user->getAvatar()) {
                        $user->setAvatar($discordUser->getAvatarHash());
                        $user->setModified(new \DateTime());

                        $this->entityManager->persist($user);
                    }
                    if ($discordUser->getGlobalName() !== $user->getUsername()) {
                        $user->setUsername($discordUser->getGlobalName());
                        $user->setModified(new \DateTime());

                        $this->entityManager->persist($user);
                    }

                    if (in_array($_ENV['DISCORD_ADMIN_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_DISCORD_ADMIN', $user->getRoles())) {
                            $user->addRole('ROLE_DISCORD_ADMIN');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_DISCORD_ADMIN', $user->getRoles())) {
                            $user->removeRole('ROLE_DISCORD_ADMIN');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    }

                    if (in_array($_ENV['DISCORD_MODERATOR_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_DISCORD_MODERATOR', $user->getRoles())) {
                            $user->addRole('ROLE_DISCORD_MODERATOR');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_DISCORD_MODERATOR', $user->getRoles())) {
                            $user->removeRole('ROLE_DISCORD_MODERATOR');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    }

                    if (in_array($_ENV['DISCORD_AUTHORIZER_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_DISCORD_AUTHORIZER', $user->getRoles())) {
                            $user->addRole('ROLE_DISCORD_AUTHORIZER');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_DISCORD_AUTHORIZER', $user->getRoles())) {
                            $user->removeRole('ROLE_DISCORD_AUTHORIZER');
                            $user->setModified(new \DateTime());

                            $this->entityManager->persist($user);
                        }
                    }
                }

                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    // public function start(Request $request, ?AuthenticationException $authException = null): Response
    // {
    //     /*
    //      * If you would like this class to control what happens when an anonymous user accesses a
    //      * protected page (e.g. redirect to /login), uncomment this method and make this class
    //      * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //      *
    //      * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //      */
    // }
}
