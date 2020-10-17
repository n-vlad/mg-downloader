<?php

namespace App\Structure;

interface BasicDataInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self;
}
