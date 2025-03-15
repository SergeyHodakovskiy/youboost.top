<?php

namespace App\Infrastructure\Translation;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MultiDomainTranslator implements LocaleAwareInterface, TranslatorBagInterface, TranslatorInterface
{
    private TranslatorInterface $translator;

    private array $domains;

    private ?string $currentLocale = null;

    public function __construct(TranslatorInterface $translator, array $domains)
    {
        $this->translator = $translator;
        $this->domains = $domains;
    }

    #[\Override]
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        if ($domain) {
            return $this->translator->trans($id, $parameters, $domain, $locale);
        }

        foreach ($this->domains as $domain) {
            $translation = $this->translator->trans($id, $parameters, $domain, $locale);
            if ($translation !== $id) {
                return $translation;
            }
        }

        return $id;
    }

    public function setLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function getLocale(): string
    {
        return $this->currentLocale ?? $this->translator->getLocale();
    }

    #[\Override]
    public function getCatalogue(?string $locale = null): \Symfony\Component\Translation\MessageCatalogueInterface
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            return $this->translator->getCatalogue($locale);
        }

        throw new \LogicException('The decorated translator does not implement TranslatorBagInterface.');
    }

    #[\Override]
    public function getCatalogues(): array
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            return $this->translator->getCatalogues();
        }

        throw new \LogicException('The decorated translator does not implement TranslatorBagInterface.');
    }

    public function addDomain(string $domain): void
    {
        if (! in_array($domain, $this->domains, true)) {
            $this->domains[] = $domain;
        }
    }

    public function removeDomain(string $domain): void
    {
        $this->domains = array_filter($this->domains, fn ($d) => $d !== $domain);
    }
}
