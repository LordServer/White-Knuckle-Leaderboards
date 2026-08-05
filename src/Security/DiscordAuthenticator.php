<?php

namespace App\Security;

use App\Entity\User;
use App\Provider\ExtendedDiscordProvider;
use App\Provider\ExtendedDiscordResourceOwner;
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
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
final class DiscordAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly ExtendedDiscordProvider $discordProvider,
        private readonly SerializerInterface $serializer,
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
                $discordUserInfo = $client->fetchUserFromToken($accessToken);
                $discordMemberInfo = $this->discordProvider->getDiscordRoles($accessToken);

                if (isset($discordMemberInfo['code'])) {
                    $discordUser['user'] = $discordUserInfo->toArray();
                    $discordUser['roles'] = [];
                    $discordUser['nick'] = null;
                    $discordUser['avatar'] = null;
                    flash()->use('theme.ruby')->info('Consider joining the official discord server!');
                } else {
                    $discordUser = $discordMemberInfo;
                }

                if (null !== $discordUser['nick']) {
                    $displayName = $discordUser['nick'];
                } elseif (null !== $discordUser['user']['global_name']) {
                    $displayName = $discordUser['user']['global_name'];
                } else {
                    $displayName = $discordUser['user']['username'];
                }

                if (null !== $discordUser['avatar']) {
                    $avatarPath = 'guilds/'.$_ENV['DISCORD_GUILD_ID'].'/users/'.$discordUser['user']['id'].'/avatars/'.$discordUser['avatar'];
                } else {
                    $avatarPath = 'avatars/'.$discordUser['user']['id'].'/'.$discordUser['user']['avatar'];
                }

                $user = $this->userRepository->findOneBy(['discord_id' => $discordUser['user']['id']]);
                $discord_roles = $discordUser['roles'];

                if (null === $user) {
                    $user = new User();
                    $user->setUsername($discordUser['user']['username']);
                    $user->setDisplayName($displayName);
                    $user->setDiscordId($discordUser['user']['id']);
                    $user->setAvatar($avatarPath);

                    // Setup initial Roles
                    if (in_array($_ENV['DISCORD_ADMIN_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_ADMIN');
                    } else {
                        $user->removeRole('ROLE_ADMIN');
                    }
                    if (in_array($_ENV['DISCORD_MODERATOR_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_MODERATOR');
                    } else {
                        $user->removeRole('ROLE_MODERATOR');
                    }
                    if (in_array($_ENV['DISCORD_AUTHORIZER_ROLE_ID'], $discord_roles)) {
                        $user->addRole('ROLE_AUTHORIZER');
                    } else {
                        $user->removeRole('ROLE_AUTHORIZER');
                    }

                    $this->entityManager->persist($user);
                } else {
                    if ($avatarPath !== $user->getAvatar()) {
                        $user->setAvatar($discordUser->getAvatarHash());

                        $this->entityManager->persist($user);
                    }

                    if ($displayName !== $user->getDisplayName()) {
                        $user->setDisplayName($displayName);

                        $this->entityManager->persist($user);
                    }

                    if (in_array($_ENV['DISCORD_ADMIN_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                            $user->addRole('ROLE_ADMIN');

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_ADMIN', $user->getRoles())) {
                            $user->removeRole('ROLE_ADMIN');

                            $this->entityManager->persist($user);
                        }
                    }

                    if (in_array($_ENV['DISCORD_MODERATOR_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_MODERATOR', $user->getRoles())) {
                            $user->addRole('ROLE_MODERATOR');

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_MODERATOR', $user->getRoles())) {
                            $user->removeRole('ROLE_MODERATOR');

                            $this->entityManager->persist($user);
                        }
                    }

                    if (in_array($_ENV['DISCORD_AUTHORIZER_ROLE_ID'], $discord_roles)) {
                        if (!in_array('ROLE_AUTHORIZER', $user->getRoles())) {
                            $user->addRole('ROLE_AUTHORIZER');

                            $this->entityManager->persist($user);
                        }
                    } else {
                        if (in_array('ROLE_AUTHORIZER', $user->getRoles())) {
                            $user->removeRole('ROLE_AUTHORIZER');

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
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        flash()->use('theme.ruby')->success('Penis has been exploded successfully!'); // TODO: update verbiage

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
