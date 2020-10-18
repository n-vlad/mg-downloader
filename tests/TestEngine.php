<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Core testing engine used by all other testing classes.
 */
class TestEngine extends WebTestCase
{
    /**
     * @var Application
     */
    protected Application $application;

    /**
     * @var string
     */
    protected string $dataPath;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);

        $this->dataPath = self::$container->getParameter('test_data');
        $this->entityManager = self::$container->get('doctrine')->getManager();

        parent::setUp();
    }

    /**
     * @param string $filename
     *   The targeted test file.
     *
     * @return array
     *   The decoded file contents.
     */
    protected function getDecodedTestFileContents(string $filename): array
    {
        return json_decode($this->getTestFileContents($filename), true);
    }

    /**
     * @param string $filename
     *   The targeted test file.
     *
     * @return string
     *   The file contents.
     */
    protected function getTestFileContents(string $filename): string
    {
        return file_get_contents($this->dataPath.DIRECTORY_SEPARATOR.$filename);
    }

    /**
     * Create a Console instance used for testing purposes.
     *
     * Passing the PHPUnit debug flag in CLI will display all error messages.
     *
     * @return SymfonyStyle
     */
    protected function getTestConsoleOutput()
    {
        $verbosity = ConsoleOutput::VERBOSITY_QUIET;
        if ($this->_isDebugOn()) {
            $verbosity = ConsoleOutput::VERBOSITY_DEBUG;
        }

        return new SymfonyStyle(new ArgvInput(), new ConsoleOutput($verbosity));
    }

    /**
     * Helper function to decide whether the test is being debugged or not.
     *
     * @return bool
     *   True if debug is on, false otherwise.
     */
    private function _isDebugOn()
    {
        global $argv;

        return in_array('--debug', $argv);
    }
}
