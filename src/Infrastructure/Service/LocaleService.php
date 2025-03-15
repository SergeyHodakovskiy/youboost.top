<?php

namespace App\Infrastructure\Service;

use App\Core\Repository\LocaleRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class LocaleService
{
    private $localeRepository;
    private $requestStack;
    private $security;
    private $translator;
    private $twig;

    private array $availableLanguages = [
        'us' => [
            'code' => 'us',
            'name' => 'English (US)',
            'flag' => [
                'viewbox' => '0 0 3900 3900',
                'paths' => [
                    ['fill' => '#b22234', 'd' => 'M0 0h7410v3900H0z'],
                    ['stroke' => '#fff', 'd' => 'M0 450h7410m0 600H0m0 600h7410m0 600H0m0 600h7410m0 600H0', 'stroke-width' => '300'],
                    ['fill' => '#3c3b6e', 'd' => 'M0 0h2964v2100H0z']
                ]
            ]
        ],
        'de' => [
            'code' => 'de',
            'name' => 'Deutsch',
            'flag' => [
                'viewbox' => '0 0 512 512',
                'paths' => [
                    ['fill' => '#ffce00', 'd' => 'M0 341.3h512V512H0z'],
                    ['d' => 'M0 0h512v170.7H0z'],
                    ['fill' => '#d00', 'd' => 'M0 170.7h512v170.6H0z']
                ]
            ]
        ],
        'it' => [
            'code' => 'it',
            'name' => 'Italiano',
            'flag' => [
                'viewbox' => '0 0 512 512',
                'paths' => [
                    ['fill' => '#fff', 'd' => 'M0 0h512v512H0z'],
                    ['fill' => '#009246', 'd' => 'M0 0h170.7v512H0z'],
                    ['fill' => '#ce2b37', 'd' => 'M341.3 0H512v512H341.3z']
                ]
            ]
        ]
    ];

    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        LocaleRepository $localeRepository,
        RequestStack $requestStack,
        Environment $twig
    ) {
        $this->security = $security;
        $this->translator = $translator;
        $this->localeRepository = $localeRepository;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    public function getCurrentLocale(): string
    {
        // 1. Check if locale is already set in session
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $sessionLocale = $session->get('_locale');
        if ($sessionLocale) {
            return $sessionLocale;
        }

        // 2. Check if logged-in user has a preferred locale
        $user = $this->security->getUser();
        if ($user && method_exists($user, 'getLocale')) {
            $userLocale = $user->getLocale();
            if ($userLocale && in_array($userLocale, $this->getLanguageCodes())) {
                $session->set('_locale', $userLocale);
                return $userLocale;
            }
        }

        // 3. Check browser's preferred language
        $browserLocales = $request->getLanguages();
        foreach ($browserLocales as $browserLocale) {
            // Extract language code (e.g., 'en' from 'en-US')
            $languageCode = substr($browserLocale, 0, 2);
            if (in_array($languageCode, $this->getLanguageCodes())) {
                $session->set('_locale', $languageCode);
                return $languageCode;
            }
        }

        // 4. Check cookies
        $cookieLocale = $request->cookies->get('_locale');
        if ($cookieLocale && in_array($cookieLocale, $this->getLanguageCodes())) {
            $session->set('_locale', $cookieLocale);
            return $cookieLocale;
        }

        // 5. Fallback to default locale from repository or hardcoded default
        $defaultLocale = $this->localeRepository->findDefaultLocale();
        $fallbackLocale = $defaultLocale ? $defaultLocale->getCode() : 'en';

        $session->set('_locale', $fallbackLocale);
        return $fallbackLocale;
    }

    public function detectLocale(): string
    {
        $defaultLocale = $this->localeRepository->findDefaultLocale();

        return $defaultLocale ? $defaultLocale->getCode() : 'en';
    }

    public function setLocale(string $localeCode, $user = null): void
    {
        $this->requestStack->getCurrentRequest()
            ->getSession()
            ->set('_locale', $localeCode);
    }

    public function getAvailableLanguages(): array
    {
        return $this->availableLanguages;
    }

    public function getLanguageDetails(string $languageCode): ?array
    {
        return $this->availableLanguages[$languageCode] ?? null;
    }

    public function getLanguageCodes(): array
    {
        return array_keys($this->availableLanguages);
    }
}
