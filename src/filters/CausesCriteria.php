<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\filters;

class CausesCriteria extends TicketsCriteria {

    /** @var string[] */
    private array $headers = [];

    public function withAcceptLanguageHeader(string $languageTag): static {
        $this->headers['Accept-Language'] = $languageTag;
        return $this;
    }

    public function getHeaders(): array {
        return $this->headers;
    }
}
