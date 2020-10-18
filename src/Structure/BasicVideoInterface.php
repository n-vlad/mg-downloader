<?php

namespace App\Structure;

interface BasicVideoInterface extends BasicDataInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url): self;

    /**
     * @return array
     */
    public function getAlternatives(): array;

    /**
     * @param array $alternatives
     *
     * @return self
     */
    public function setAlternatives(array $alternatives): self;
}
